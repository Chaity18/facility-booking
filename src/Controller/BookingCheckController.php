<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use stdClass;

class BookingCheckController extends AbstractController
{

    /**
     * @Route("/booking/check", name="app_booking_check")
     */
    public function index(): Response
    { 
        $usersBooking = $this->getBookings();

        foreach($usersBooking as $booking) {
            $this->calculateAndBook($booking);
        }
        
        return $this->render('booking_check/index.html.twig', [
            'bookings' => $usersBooking,
        ]);
    }

    public function calculateAndBook(object $booking)
    {
        $userBookings = $this->getBookings();
        foreach($userBookings as $usersBooking) {
            if($booking->startTime == $usersBooking->startTime && $booking->endTime == $usersBooking->endTime && $usersBooking->id == $booking->id) {
                $booking->total = null;
                $booking->booked = false;  
            }else{
                $perHourCharge = 0;
                if($booking->service == 'club_house') {
                    $perHourCharge = 100;
                    $endTime = explode(' ',$booking->endTime)[1];
                    if(strtotime($endTime) >= strtotime('16:00:00')) {
                        $perHourCharge = 500; 
                    } 
                }elseif ($booking->service == 'tenis_court') {
                        $perHourCharge = 50;
                }
        
                $timeDifference = $this->getTimeDifference(new DateTime($booking->startTime), new DateTime($booking->endTime));
                $total = $timeDifference * $perHourCharge;
                $booking->total = $total;
                $booking->booked = true;
            }
        }

        return $booking;
    }
    
    public function getTimeDifference(DateTime $startDateTime, DateTime $endDateTime) 
    {
        $dateDiff = $startDateTime->diff($endDateTime);
        return $dateDiff->h;
    } 

    public function getBookings()
    {
        $bookingOne = new stdClass();
        $bookingOne->id = 1;
        $bookingOne->service = 'club_house';
        $bookingOne->startTime = '2021-10-26 16:00:00';
        $bookingOne->endTime = '2021-10-26 22:00:00';

        $bookingTwo = new stdClass();
        $bookingTwo->id = 2;
        $bookingTwo->service = 'tenis_court';
        $bookingTwo->startTime = '2021-10-26 10:00:00';
        $bookingTwo->endTime = '2021-10-26 20:00:00';

        $bookingThree = new stdClass();
        $bookingThree->id = 3;
        $bookingThree->service = 'club_house';
        $bookingThree->startTime = '2021-10-26 16:00:00';
        $bookingThree->endTime = '2021-10-26 22:00:00';

        return array($bookingOne, $bookingTwo, $bookingThree);
    }
}
