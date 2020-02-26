<?php

namespace EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// Include the used classes as JsonResponse and the Request object
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

// The entity of your Appointment
use EventBundle\Entity\Appointments as Appointment;

class SchedulerController extends Controller
{
    /**
     * View that renders the scheduler.
     *
     */
    public function indexAction()
    {
        // Retrieve entity manager
        $em = $this->getDoctrine()->getManager();

        // Get repository of appointments
        $repositoryAppointments = $em->getRepository("EventBundle:Appointments");

        // Note that you may want to filter the appointments that you want to send
        // by dates or something, otherwise you will send all the appointments to render
        $appointments = $repositoryAppointments->findAll();

        // Generate JSON structure from the appointments to render in the start scheduler.
        $formatedAppointments = $this->formatAppointmentsToJson($appointments);

        // Render scheduler
        return $this->render("default/scheduler.html.twig", [
            'appointments' => $formatedAppointments
        ]);
    }

    public function iframeAction()
    {
        // Retrieve entity manager
        $em = $this->getDoctrine()->getManager();

        // Get repository of appointments
        $repositoryAppointments = $em->getRepository("EventBundle:Appointments");

        // Note that you may want to filter the appointments that you want to send
        // by dates or something, otherwise you will send all the appointments to render
        $appointments = $repositoryAppointments->findAll();

        // Generate JSON structure from the appointments to render in the start scheduler.
        $formatedAppointments = $this->formatAppointmentsToJson($appointments);

        // Render scheduler
        return $this->render("default/iframe.html.twig", [
            'appointments' => $formatedAppointments
        ]);
    }

    /**
     * Handle the creation of an appointment.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryAppointments = $em->getRepository("EventBundle:Appointments");

        // Use the same format used by Moment.js in the view
        $format = "d-m-Y H:i:s";

        // Create appointment entity and set fields values
        $appointment = new Appointment();
        $appointment->setTitle($request->request->get("title"));
        $appointment->setDescription($request->request->get("description"));
        $appointment->setStartDate(
            \DateTime::createFromFormat($format, $request->request->get("start_date"))
        );
        $appointment->setEndDate(
            \DateTime::createFromFormat($format, $request->request->get("end_date"))
        );

        // Create appointment
        $em->persist($appointment);
        $em->flush();

        return new JsonResponse(array(
            "status" => "success"
        ));
    }

    /**
     * Handle the update of the appointments.
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryAppointments = $em->getRepository("EventBundle:Appointments");

        $appointmentId = $request->request->get("id");

        $appointment = $repositoryAppointments->find($appointmentId);

        if (!$appointment) {
            return new JsonResponse(array(
                "status" => "error",
                "message" => "The appointment to update $appointmentId doesn't exist."
            ));
        }

        // Use the same format used by Moment.js in the view
        $format = "d-m-Y H:i:s";

        // Update fields of the appointment
        $appointment->setTitle($request->request->get("title"));
        $appointment->setDescription($request->request->get("description"));
        $appointment->setStartDate(
            \DateTime::createFromFormat($format, $request->request->get("start_date"))
        );
        $appointment->setEndDate(
            \DateTime::createFromFormat($format, $request->request->get("end_date"))
        );

        // Update appointment
        $em->persist($appointment);
        $em->flush();

        return new JsonResponse(array(
            "status" => "success"
        ));
    }

    /**
     * Deletes an appointment from the database
     *
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryAppointments = $em->getRepository("EventBundle:Appointments");

        $appointmentId = $request->request->get("id");

        $appointment = $repositoryAppointments->find($appointmentId);

        if (!$appointment) {
            return new JsonResponse(array(
                "status" => "error",
                "message" => "The given appointment $appointmentId doesn't exist."
            ));
        }

        // Remove appointment from database !
        $em->remove($appointment);
        $em->flush();

        return new JsonResponse(array(
            "status" => "success"
        ));
    }


    /**
     * Returns a JSON string from a group of appointments that will be rendered on the calendar.
     * You can use a serializer library if you want.
     *
     * The dates need to follow the format d-m-Y H:i e.g : "13-07-2017 09:00"
     *
     *
     * @param $appointments
     */
    private function formatAppointmentsToJson($appointments)
    {
        $formatedAppointments = array();

        foreach ($appointments as $appointment) {
            array_push($formatedAppointments, array(
                "id" => $appointment->getId(),
                "description" => $appointment->getDescription(),
                // Is important to keep the start_date, end_date and text with the same key
                // for the JavaScript area
                // altough the getter could be different e.g:
                // "start_date" => $appointment->getBeginDate();
                "text" => $appointment->getTitle(),
                "start_date" => $appointment->getStartDate()->format("Y-m-d H:i"),
                "end_date" => $appointment->getEndDate()->format("Y-m-d H:i")
            ));
        }

        return json_encode($formatedAppointments);
    }

    public function frontAction()
    {
        $em = $this->getDoctrine()->getManager();

        $appointments = $em->getRepository('EventBundle:Appointments')->findAll();

        return $this->render('default/card.html.twig', array(
            'appointments' => $appointments,
        ));

    }
    public function formAction()
    {
        return $this->render('default/form.html.twig');

    }

}