<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Session;
use DateTime;
use DateTimeZone;

class TaskController extends Controller
{
    /**
     * Paginate the authenticated user's tasks.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $todos = $this->getTask($request->timezone);

        return response()->json(['tasks' => $todos]);
    }

    /**
     * Store a new incomplete task for the authenticated user.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // validate the given request
        $data = $this->validate($request, [
            'title' => 'required|string|max:255',
            'deadline' => 'required',
        ]);

        $timezone_name = timezone_name_from_abbr("", $request->tz*60, false);
        $timezone_name = empty($timezone_name) ? 'Asia/Karachi' : $timezone_name;
        date_default_timezone_set($timezone_name);

        // create a new incomplete task with given title
        Task::create([
            'title' => $data['title'],
            'deadline_utc' => $this->convertLocalToUTC($data['deadline']),
            'deadline_local' => date("Y-m-d H:i:s",strtotime($data['deadline'])),
            'local_timezone' => $timezone_name,
            'is_complete' => false,
        ]);

        $todos = $this->getTask($request->timezone);

        return response()->json(["message" => "Task created." , 'tasks' => $todos]);
    }

    /**
     * Mark the given task as complete and redirect to tasks index.
     *
     * @param Task $task
     * @return Redirector
     * @throws AuthorizationException
     */
    public function update(Request $request)
    {
        // check if the authenticated user can complete the task
        // $this->authorize('complete', $task);
        $task = Task::find($request->id);
        // mark the task as complete and save it
        $task->is_complete = true;
        $task->save();

        $todos = $this->getTask($request->timezone);

        return response()->json(["message" => "Task updated." , 'tasks' => $todos]);
    }

    public function convertLocalToUTC($datetime)
    {
        $dateTime = date("Y-m-d H:i:s",strtotime($datetime)); 
        $newDateTime = new \DateTime($dateTime); 
        $newDateTime->setTimezone(new \DateTimeZone("UTC")); 
        $dateTimeUTC = $newDateTime->format("Y-m-d H:i:s");
        return $dateTimeUTC;
    }

    public function convertUTCToLocal($datetime,$timezone)
    {
        $dateTime = date("Y-m-d H:i:s",strtotime($datetime)); 
        $newDateTime = new \DateTime($dateTime); 
        $newDateTime->setTimezone(new \DateTimeZone($timezone)); 
        $dateTimeLocal = $newDateTime->format("h:i a, jS F");        
        return $dateTimeLocal;
    }

    public function getTask($timezone){
        $tasks = Task::orderBy('is_complete')
            ->orderByDesc('created_at')->get();

        $timezone_name = timezone_name_from_abbr("", $timezone*60, false);
        $timezone_name = empty($timezone_name) ? 'Asia/Karachi' : $timezone_name;
        $todos = [];

        foreach ($tasks as $task) {
            $todos[] = ['id' => $task->id, 'title' => $task->title, 'deadline' => $this->convertUTCToLocal($task->deadline_utc,$timezone_name), 'is_complete' => $task->is_complete];
        }
        return $todos;
    }
}
