<?php

namespace App\Listeners;

use App\Services\User\UserService;
use App\Events\TCTStudentInfoUpdateRequested;

class UpdateTCTStudentInfo
{
    protected $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     *
     * @param TCTStudentInfoUpdateRequested $event
     *
     * @return void
     */
    public function handle(TCTStudentInfoUpdateRequested $event)
    {
        // echo("HERE");
        // $info = $this->userService->updateTCTStudentInfo($event->request,$event->student_id);
        try {
            $this->userService->updateTCTStudentInfo($event->request, $event->student_id);

            return true;
        } catch (\Exception $ex) {
            Log::info('Failed to update Student information, Id: '.$event->user_id);

            return false;
        }
    }
}
