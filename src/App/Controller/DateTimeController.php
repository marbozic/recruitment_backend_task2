<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Timezones;

class DateTimeController extends AbstractController
{
    /**
     * @Route("/datetime", name="app_date_time_index")
     */
    public function index(Request $request): Response
    {
        $date = $timezone = null;
        $dateError = $timezoneError = null;
        $textResult = null;
        
        if ($request->getMethod() == Request::METHOD_POST) {
            $date = $request->request->get('date');
            $timezone = $request->request->get('timezone');
            
            $isValidTimezone = Timezones::exists($timezone); 
            $dateObject = \DateTime::createFromFormat('Y-m-d', $date);
            
            if (!$dateObject instanceof \DateTime) {
                $dateError = 'Date is not valid!';
            }
            
            if (!$isValidTimezone) {
                $timezoneError = 'Timezone is not valid!';
            }
            
            if ($dateObject instanceof \DateTime && $isValidTimezone) {
                $utc =  Timezones::getRawOffset($timezone) / 60;
                $lastDateFeb = date_create('Last Day of February ' . $dateObject->format('Y'));
                $dayFeb = $lastDateFeb->format('d');
                $month = $dateObject->format('F');
                $lastDateMonth = date_create('Last Day of ' . $month . ' ' . $dateObject->format('Y'));
                $day = $lastDateMonth->format('d');
                
                $textResult = 'The timezone ' . $timezone . ' has ' . ($utc > 0 ? ('+' . $utc) : $utc) . ' minutes offset to UTC on the given day at noon.<br />' 
                        . 'February in this year is ' . $dayFeb . ' days long.<br />'
                        . 'The specified month (' . $month . ') is ' . $day . ' days.';
            }
        }
        
        return $this->render('datetime/index.html.twig', [
            'date' => $date,
            'date_error' => $dateError,
            'timezone' => $timezone,
            'timezone_error' => $timezoneError,
            'text_result' => $textResult
        ]);
    }
}
