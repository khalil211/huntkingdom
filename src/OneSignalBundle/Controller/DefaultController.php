<?php

namespace OneSignalBundle\Controller;

use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class DefaultController extends Controller
{
    public function indexAction()
    {
        $content = array(
            "en" => 'Testing Message'
        );

        $fields = array(
            'app_id' => "b8f3f614-4701-407c-b1e9-5e35d5775757",
            'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
            'large_icon' =>"ic_launcher_round.png",
            'contents' => $content
        );

        $fields = json_encode($fields);

        try{

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"app_id\": \"b8f3f614-4701-407c-b1e9-5e35d5775757\",\n\"contents\": {\"en\": \"nouveau offre envoye a ingenschool\"},\n\"included_segments\": [\"Subscribed Users\"]}");

            $headers = array();
            $headers[] = 'Content-Type: application/json; charset=utf-8';
            $headers[] = 'Authorization: Basic OWRkOTBkNmUtNTU0YS00ZTY3LThhYWUtOTY5YjU2Y2E5Y2Jm';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);


        }catch(\Exception $e) {


        }

        return $this->redirectToRoute('frame');

    }

}
