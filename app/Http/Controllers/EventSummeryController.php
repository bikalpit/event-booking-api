<?php

namespace App\Http\Controllers;
use App\EtOrders;
use App\EtEventTicketRevenue;
use App\EtEventTicket;
use App\EtEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
class EventSummeryController extends Controller
{
    public function __construct()
    {
        //
    }

    public function getEventSummery(Request $request)
    {
        $this->validate($request, [			
			      'event_id'=>'required'
        ]);

        $getEeventDate = EtEvent::select('start_date')->where('unique_code',$request->event_id)->first();
        if($getEeventDate)
        {
            $startDate = Carbon::parse($getEeventDate->start_date);
            $today = Carbon::parse(Carbon::Today()->format('Y-m-d'));
            $diffDays = $today->diffInDays($startDate);
            $totalIssueTicket = EtEventTicketRevenue::where('event_id',$request->event_id)->sum('sold');
            $totalRevenue = EtEventTicketRevenue::where('event_id',$request->event_id)->sum('revenue');
            $totalView   = "10";
            $totalTicket = ["issueTicket"=>$totalIssueTicket,"salesRevenue"=>$totalRevenue,"daysToGo"=>$diffDays,"eventView"=>$totalView];

            $soldTicket = EtEventTicketRevenue::with(['event' => function($query){
                $query->select(['unique_code','event_title']);
            }])->where('event_id',$request->event_id)->get();

            $finalSold = [];
            $finalView = [];
            $graphFirstDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $graphSecondDate = Carbon::now()->subDays(6)->format('Y-m-d');
            $graphThirdDate = Carbon::now()->subDays(5)->format('Y-m-d');
            $graphFourthDate = Carbon::now()->subDays(4)->format('Y-m-d');
            $graphFifthDate = Carbon::now()->subDays(3)->format('Y-m-d');
            $graphSixthDate = Carbon::now()->subDays(2)->format('Y-m-d');
            $graphSeventhDate = Carbon::now()->subDays(1)->format('Y-m-d');
            
            $dateArray = [$graphFirstDate,$graphSecondDate,$graphThirdDate,$graphFourthDate,$graphFifthDate,$graphSixthDate,$graphSeventhDate,date('Y-m-d')];

            foreach ($dateArray as $date) {
                $sold = EtEventTicket::where('created_at', 'like', '%'.$date.'%')->count();
                $finalSold[] = array("date"=>date('m/d', strtotime($date)),"sale"=>$sold);
            }

            foreach ($dateArray as $date) {
                $dateSold['view'] = '10';
                $finalView[] = array("date"=>$date,"sale"=>$dateSold['view']);
            }

            $finalArry = ["totalTicket"=>$totalTicket,"soldTicket"=>$soldTicket,"graphSale"=>$finalSold,"graphViews"=>$finalView];
            return $this->sendResponse($finalArry);
        }
        else
        {
            return $this->sendResponse("Sorry! Something wrong.",200,false);
        }
    }
}
