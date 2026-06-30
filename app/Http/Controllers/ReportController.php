<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Notifications\AdminNotification;
use App\Models\Admin;
use App\Events\NotificationPusher;


class ReportController extends Controller
{
    public function create($type,$otherId)
    {
        return view('report', ['type' => $type,'otherId'=>$otherId]);
    }
    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'otherId' => 'required|integer',
            'type' => 'required|string',
            'report' => 'required|string|max:255',
        ]);

        $report = new Report();
        $report->id_user = Auth::id();
        $report->reportable_id = $request->input('otherId');
        $reportableType = $request->input('type');
        if (!in_array($reportableType, ['user', 'post','group'])) {
            return redirect()->back()->with('error', 'Invalid reportable type.');
        }
        $report->reportable_type = $reportableType;
        $report->description = $request->input('report');
        $report->created_at = now();
        $report->updated_at = now();
        $report->save();

        $adminUsers=Admin::all();
        if ($adminUsers->isEmpty()) {
            return redirect()->back()->with('error', 'No administrators found.');
        }

        foreach($adminUsers as $adminUser){
            $notification = new Notification();
            $notification->emitter_user = Auth::id();
            $notification->received_user = $adminUser->id_user;
            $notification->date = now();
            $notification->save();

            $adminNotification = new AdminNotification();
            $adminNotification->id = $notification->id;
            $adminNotification->id_admin = $adminUser->id_user;
            $adminNotification->id_report = $report->id;
            if($reportableType == 'user'){
                $adminNotification->notification_type = 'report_user';
            }else if($reportableType == 'post'){
                $adminNotification->notification_type = 'report_post';
            }else if($reportableType == 'group'){
                $adminNotification->notification_type = 'report_group';
            }
            
            $adminNotification->save();

            event(new NotificationPusher($notification->id, $notification->received_user));

        }
        
        return redirect()->route('report.create', ['type' => $request->input('type'),'otherId'=> $request->input('otherId')])->with('success', 'Report submitted successfully!');
    }

    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        $this->authorize('viewAny', Report::class);

        $reports = Report::with('reporter', 'reportable')->get();

        return view('reports.index', compact('reports'));
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        if(!Auth::user()->isAdmin()){
            return redirect()->back()->with('error', 'You are not authorized to delete reports.');
        }
        $report = Report::findOrFail($id);

        $this->authorize('delete', $report);

        $report->delete();

        return redirect()->back()->with('success', 'Report deleted successfully!');
    }
}