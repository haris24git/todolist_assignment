<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #app {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            {{--  width: 600px;  --}}
        }
        .table-responsive {
            margin-top: 20px;
        }
        .task-item {
            font-size: 1.1em;
        }
        .task-actions button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div id="app" class="container">
        <h3 class="text-center mb-4">Task Manager</h3>

        <div class="input-group mb-3">
            <input type="text" id="task-input" class="form-control" placeholder="Enter task">
            <button class="btn btn-primary" onclick="addTask()">Add Task</button>
        </div>


        <div class="text mb-3">
            <button class="btn btn-secondary" >To-do List</button>
            {{--  <h3>To-do List</h3>  --}}
        </div>



        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Id.</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="task-list">
                    @foreach($tasks as $task)
                        <tr class="task-item" data-id="{{ $task->id }}">
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->task }}</td>
                            <td>
                                <span class="completed-status text-{{ $task->completed ? 'success' : 'warning' }}">
                                    {{ $task->completed ? 'Completed' : 'Pending' }}
                                </span>
                            </td>
                            <td class="task-actions">
                                @if(!$task->completed)
                                {{--  <button class="btn btn-success btn-sm" onclick="markAsCompleted(this)">Update</button>  --}}
                                <input type="checkbox" class="form-check-input" style="border-color: chocolate;" onclick="markAsCompleted(this)">
                                @endif
                                <button class="btn btn-danger btn-sm" onclick="deleteTask(this)">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        function addTask() {
            const taskInput = $('#task-input').val().trim();

            if (taskInput === '') return;

            $.ajax({
                url: '/tasks/store',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    task: taskInput
                },
                success: function(task) {
                    $('#task-input').val('');
                    const listItem = `
                        <tr class="task-item" data-id="${task.id}">
                            <td>${task.id}</td>
                            <td>${task.task}</td>
                            <td>
                                <span class="completed-status text-warning">Pending</span>
                            </td>
                            <td class="task-actions">
                                <input type="checkbox" class="form-check-input" onclick="markAsCompleted(this)" style="border-color: chocolate;">
                                <button class="btn btn-danger btn-sm" onclick="deleteTask(this)">Delete</button>
                            </td>
                        </tr>`;
                    $('#task-list').append(listItem);
                },
                error: function() {
                    alert('Task already exists or an error occurred.');
                }
            });
        }

        function markAsCompleted(button) {
            const listItem = $(button).closest('tr');
            const taskId = listItem.data('id');

            $.ajax({
                url: '/tasks/update',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    id: taskId,
                    completed: 1
                },
                success: function(response) {
                    if (response.success) {
                        listItem.find('.completed-status').removeClass('text-warning').addClass('text-success').text('Completed');
                        $(button).remove(); // it will remove the checkbox after clicking on it
                    } else {
                        alert('Failed to update the task.');
                    }
                },
                error: function() {
                    alert('An error occurred while updating the task.');
                }
            });
        }



        function deleteTask(button) {
            if (confirm('Are you sure to delete this task?')) {
                const listItem = $(button).closest('tr');
                const taskId = listItem.data('id');

                $.ajax({
                    url: '/tasks/delete',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        id: taskId
                    },
                    success: function() {
                        listItem.remove();
                    }
                });
            }
        }

        function showAllTasks() {
            $('tr').show();
        }
    </script>
</body>
</html>
