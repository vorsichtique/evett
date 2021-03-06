<?php


namespace App\Controller;


use App\Import\Importer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhooks", name="webhooks_")
 */
class WebHookController extends Controller
{

    const NUMBER_OF_DAYS_IN_ADVANCE = 30;
    /**
     * @Route("/import-events", name="import_events")
     */
    public function importEvents(Importer $importer){
        $day = Carbon::today();
        $client = new  Client();
        $message = [];

        for($i=0; $i < self::NUMBER_OF_DAYS_IN_ADVANCE; $i++){

            if($i < 15){
                if($importMessage = $importer->import($day->copy())->getMessage()){
                    $message[] = $importMessage;
                };
                continue;
            }

            if(6 === $day->dayOfWeek || 7 === $day->dayOfWeek){
                if($importMessage = $importer->import($day->copy())->getMessage()) {
                    $message[] = $importMessage;
                }
            }
            $day->addDay();
        }

        if(0 === count($message)) {
            return new JsonResponse('No events imported');
        }

        $message[] = $this->generateUrl('new_events');

        $message = implode(PHP_EOL, $message);

        $client->request('POST', getenv('WEBHOOK_SEND_MESSAGE'), ['json' => ['text' => $message]]);
        return new JsonResponse('Imported for the next ' . self::NUMBER_OF_DAYS_IN_ADVANCE . ' days');
    }
}