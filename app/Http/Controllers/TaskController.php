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
    public function index()
    {
        return view('tasks');
    }
    public function getTasks(Request $request)
    {
        // paginate the authorized user's tasks with 5 per page
        $tasks = Task::orderBy('is_complete')
            ->orderByDesc('created_at')
            ->paginate(5);

        $timezone_name = timezone_name_from_abbr("", $request->timezone*60, false);
        $timezone_name = empty($timezone_name) ? 'Asia/Karachi' : $timezone_name;
        $todos = [];

        foreach ($tasks as $task) {
            $todos[] = ['id' => $task->id, 'title' => $task->title, 'deadline' => $this->convertUTCToLocal($task->deadline_utc,$timezone_name), 'is_complete' => $task->is_complete];
        }

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

        // flash a success message to the session
        session()->flash('status', 'Task Created!');

        // redirect to tasks index
        return redirect('/tasks');
    }

    /**
     * Mark the given task as complete and redirect to tasks index.
     *
     * @param Task $task
     * @return Redirector
     * @throws AuthorizationException
     */
    public function update(Task $task)
    {
        // check if the authenticated user can complete the task
        // $this->authorize('complete', $task);

        // mark the task as complete and save it
        $task->is_complete = true;
        $task->save();

        // flash a success message to the session
        session()->flash('status', 'Task Completed!');

        // redirect to tasks index
        return redirect('/tasks');
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
}
