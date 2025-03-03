<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Tickets;
use App\Models\TicketsCategories;
use App\Models\Department;
use App\Models\User;

class TicketingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view ticketing', ['only' => ['index']]);
        $this->middleware('permission:show ticketing', ['only' => ['show']]);
        $this->middleware('permission:create ticketing', ['only' => ['create', 'store']]);
        $this->middleware('permission:update ticketing', ['only' => ['edit', 'update']]);
        $this->middleware('permission:handle ticketing', ['only' => ['handle']]);
        $this->middleware('permission:comment ticketing', ['only' => ['comment']]);
        $this->middleware('permission:solved ticketing', ['only' => ['solved']]);
        $this->middleware('permission:cenceled ticketing', ['only' => ['cenceled']]);
        $this->middleware('permission:delete ticketing', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil user yang sedang login dan relasi employe
        $user = User::with(['employe', 'roles'])->find(auth()->user()->id);
        $isSuperadmin = $user->roles->where('name', 'Superadmin')->isNotEmpty();

        // Query dasar untuk tiket
        $ticketsQuery = Tickets::with(['category', 'assignee', 'user', 'fixed'])
            ->orderBy('id', 'desc');

        // Clone query untuk widget sebelum paginasi
        $widgetQuery = clone $ticketsQuery;

        // Filter tiket berdasarkan role user 
        if (!$isSuperadmin) {
            // Admin melihat tiket berdasarkan department_id dari assignee
            $ticketsQuery->whereHas('assignee', function ($query) use ($user) {
                $query->where('assigned_id', $user->employe->department_id);
            });

            $widgetQuery->whereHas('assignee', function ($query) use ($user) {
                $query->where('assigned_id', $user->employe->department_id);
            });
        }

        // Paginasi tiket setelah filtering
        $tickets = $ticketsQuery->paginate(12);

        // Hitung widget setelah filtering
        $widget = $widgetQuery->selectRaw('
            SUM(CASE WHEN status = "Open" THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = "Closed" THEN 1 ELSE 0 END) as closed,
            SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as inprogress,
            SUM(CASE WHEN status = "Cancelled" THEN 1 ELSE 0 END) as cancelled,
            COUNT(*) as total
        ')->first();

        // Tampilkan view dengan data tiket dan widget
        return view('ticketing.index', compact('tickets', 'widget'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = TicketsCategories::all();
        $departments = Department::all();
        $tickets = Tickets::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        return view('ticketing.create', compact('tickets', 'categories', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation request
        $request->validate([
            'category_id' => 'required|exists:tickets_categories,id',
            'assignee_to' => 'required|exists:departments,id',
            'title' => 'required|string|max:100',
            'priority' => 'required|string|in:Low,Medium,High',
            'description' => 'required',
        ]);

        $description = $this->processDescriptionImages($request->description);

        // Generate code Ticket EX: Ticket-00001
        $code = 'Ticket-' . str_pad(Tickets::count() + 1, 5, '0', STR_PAD_LEFT);

        // Store data
        $data = [
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'assigned_id' => $request->assignee_to,
            'code' => $code,
            'title' => $request->title,
            'description' => $description,
            'priority' => $request->priority,
        ];

        DB::beginTransaction();
        try {
            $ticket = Tickets::create($data); // Save ticket with attachments
            DB::commit();
            return redirect()->back()->with('success', 'Ticket ' . $ticket->code . ' created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tickets = Tickets::with('category', 'assignee', 'user', 'fixed', 'comments')->findOrFail($id);
        return view('ticketing.show', compact('tickets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request data
            $request->validate([
                'category_id' => 'required|exists:tickets_categories,id',
                'assignee_to' => 'required|exists:departments,id',
                'title' => 'required|string|max:100',
                'priority' => 'required|string|in:Low,Medium,High',
                'description' => 'required|string',
            ]);

            // Find ticket or throw 404
            $ticket = Tickets::findOrFail($id);

            // Process description HTML and handle images
            $description = $this->processDescriptionImages($request->description);

            // Prepare update data
            $data = [
                'category_id' => $request->category_id,
                'assigned_id' => $request->assignee_to,
                'title' => $request->title,
                'priority' => $request->priority,
                'description' => $description,
                'updated_at' => now(),
            ];

            DB::beginTransaction();

            // Update ticket
            $ticket->update($data);
            // Log the ticket update
            DB::commit();
            return redirect()->back()->with('success', "Ticket {$ticket->code} updated successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $ticket = Tickets::findOrFail($id);
            $ticket->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Ticket ' . $ticket->code . ' deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle the specified resource from storage.
     */
    public function handle($id)
    {
        DB::beginTransaction();
        try {
            $ticket = Tickets::findOrFail($id);
            $ticket->update([
                'status' => 'In Progress',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Ticket ' . $ticket->code . ' handled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function comment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Tickets::findOrFail($id);
            $ticket->comments()->create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'comment' => $request->comment,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Comment added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function solved(Request $request, $id)
    {
        // Validation request
        $request->validate([
            'solution' => 'required',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Process description HTML and handle images
        $attachment = $this->processDescriptionImages($request->attachment);

        $request->merge([
            'solution' => $attachment
        ]);

        $data = [
            'user_by' => auth()->user()->id,
            'solution' => $request->solution,
            'attachment' => $request->attachment,
            'close_date' => now(),
            'status' => 'Closed',
        ];

        DB::beginTransaction();
        try {
            $ticket = Tickets::findOrFail($id);
            $ticket->update($data);
            DB::commit();
            return redirect()->back()->with('success', 'Ticket ' . $ticket->code . ' solved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function cancled(Request $request, $id)
    {
        // Validation request
        $request->validate([
            'reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Tickets::findOrFail($id);
            $ticket->update([
                'user_by' => auth()->user()->id,
                'reason' => $request->reason,
                'status' => 'Cancelled',
                'closed_date' => now(),
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Ticket ' . $ticket->code . ' cancled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process HTML description and handle embedded images
     *
     * @param string $description
     * @return string
     */
    private function processDescriptionImages(string $description): string
    {
        // Configure DOMDocument
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHtml($description, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        // Process each image
        foreach ($images as $image) {
            $imageSource = $image->getAttribute('src');

            // Skip if not base64 encoded
            if (!str_contains($imageSource, 'base64,')) {
                continue;
            }

            try {
                // Extract and decode base64 image
                $base64Data = explode(',', explode(';', $imageSource)[1])[1];
                $imageData = base64_decode($base64Data);

                if ($imageData === false) {
                    continue;
                }

                // Generate unique filename
                $filename = 'tickets/attachment/' . time() . '.png';

                // Store image
                Storage::disk('public')->put($filename, $imageData);

                // Update image source
                $image->removeAttribute('src');
                $image->setAttribute('src', Storage::url($filename));
            } catch (\Exception $e) {
                // Handle image processing error
                return $e->getMessage();
                continue;
            }
        }

        return $dom->saveHTML();
    }
}
