<?php
echo "Hello this is a cli tool using php to Track Tasks!\n
You can create, update, view and delete tasks.\n";
$contents = [];
if (($file = fopen('tasks.json', 'r', false,)) && filesize("tasks.json") > 0) {
    $contents = json_decode(fread($file, filesize("tasks.json")), true);
    foreach ($contents as $id => $content) {
        listContent($content, $id);
        echo "\n";
    }
    fclose($file);
} else {
    echo "Could not find file tasks.json or it is empty\n";
}
$stop_app = 0;
while ($stop_app!== 1) {
    echo "\n\nOptions:\n
    1 - To add new task\n
    2 - To update task by available id\n
    3 - To view task\n
    4 - List all tasks\n
    5 - To delete task by id\n
    6 - Save to file\n
    q - To exit app\n";
    echo "\n\n";
    $input = trim(readline("Enter option: "));
    echo "\n";
    switch($input) {
        case "1":
            addTask($contents);
            break;
        case "2":
            if (count($contents)) {
                updateTask($contents);
            } else {
                echo "There are no tasks available\n";
            }
            break;
        case "3":
            if (count($contents)) {
                viewTask($contents);
            } else {
                echo "There are no tasks available\n";
            }
            break;
        case "4":
            if (count($contents)) {
                viewAll($contents);
            } else {
                echo "There are no tasks available\n";
            }
            break;
        case "5":
            if (count($contents)) {
                removeTask($contents);
            } else {
                echo "There are no tasks available\n";
            }
            break;
        case "6":
            saveToFile($contents);
            break;
        case "q":
            $stop_app = 1;
            break;
        default:
            $stop_app = 1;
            break;
    }
}
echo "Exited app\n";
function addTask(array &$content): void
{
    $id = (int) readline("Set id, if it exists, you will be prompted again.\n ");
    while(key_exists($id, $content)) {
        $id = readline("Set id, if it exists, you will be prompted again.\n ");
    }    
    $description = readline("Add a description to the task.\n ");
    $status = readline("Set status, 1 for 'todo', 2 for 'in-progress' or 3 for 'done'.\n ");
    while(!($status > 0 && $status < 4)) {
        $status = readline("Set status, 1 for 'todo', 2 for 'in-progress' or 3 for 'done'.\n");
    }
    $created_at = date('d.m.Y H:i:s');
    $updated_at = "";
    $content[$id] = [
        'description' => $description,
        'status'      => $status,
        'created_at'  => $created_at,
        'updated_at'  => $updated_at
    ];
    echo "Task with id {$id} created successfully!\n ";
}
function updateTask(array &$content): void
{
    $id = readline("Get id from range of ids otherwise you will be prompted again.\n ");
    while(!key_exists($id, $content)) {
        $id = readline("Get id from range of ids otherwise you will be prompted again.\n ");
    }
    listContent($content[$id], $id);
    $content[$id]['description'] = readline("Type in new description.\n ");
    $status = readline("Set NEW status, 1 for 'todo', 2 for 'in-progress' or 3 for 'done'.\n ");
    while(!($status > 0 && $status < 4)) {
        $status = readline("Set NEW status, 1 for 'todo', 2 for 'in-progress' or 3 for 'done'.\n");
    }
    $content[$id]['status'] = $status;
    $content['updated_at'] = date('d.m.Y H:i:s');
    echo "Task with id {$id} updated successfully!\n ";
}
function viewTask(array &$content): void
{
    $id = readline("Get id from range of ids otherwise you will be prompted again.\n ");
    while(!key_exists($id, $content)) {
        $id = readline("Get id from range of ids otherwise you will be prompted again.\n ");
    }
    listContent($content[$id], $id);
}
function viewAll(array &$contents): void
{
    foreach ($contents as $id => $content) {
        listContent($content, $id);
        echo "\n";
    }
}
function removeTask(array &$content): void
{
    $id = readline("Remove task from range of available ids otherwise you will be prompted again.\n ");
    while(!key_exists($id, $content)) {
        $id = readline("Remove task from range of available ids otherwise you will be prompted again.\n ");
    }
    unset($content[$id]);
    echo "Removed task with {$id}\n";
}
function saveToFile(array &$content): void
{
    $res = file_put_contents('tasks.json', json_encode($content));
    if ($res) {
        echo "Written {$res} bytes!\n";
    } else {
        echo "Failed to write!\n";
    }
}
function listContent(array $content, int $id): void
{
    echo "Id: {$id}\n";
    echo "Description: {$content['description']}\n";
    echo "Status: " . status($content['status']) . "\n";
    echo "Created at: {$content['created_at']}\n";
    echo "Updated at: {$content['updated_at']}\n\r";
}
function status($index): string
{
    switch($index) {
        case 1:
            return 'todo';
        case 2:
            return 'in-progress';
        case 3:
            return 'done';
        default:
            return '';
    }
}
?>