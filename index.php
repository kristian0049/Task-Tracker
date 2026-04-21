<?php
$tasks = file_get_contents('tasks.json');
if ($tasks) {
    $tasks = json_decode($tasks, true);
} else {
    $tasks = [];
}
$file = "tasks.json";
if(isset($argv[1]) && strlen(trim($argv[1])) && is_array($tasks)) {
    switch ($argv[1]) {
        case "add":
            if (isset($argv[2])) {
                addTask($file, $tasks, $argv[2]);
            } else {
                echo "No description added\n";
            }
            break;
        case "update":
            if (isset($argv[2]) && isset($argv[3])) {
                updateTask($file, $tasks, $argv[2], $argv[3]);
            } else {
                echo "Need argument 'id' followed by 'description'\n";
            }
            break;
        case "delete":
            if (isset($argv[2])) {
                removeTask($file, $tasks, $argv[2]);
            } else {
                echo "Need 'id' \n";
            }
            break;
        case "list":
            if (isset($argv[2]) && strlen(trim($argv[2]))){
                viewByStatus($tasks, $argv[2]);
            } else {
                viewAll($tasks);
            }
            break;
        case "mark-in-progress":
            if (isset($argv[2])) {
                markInProgress($file, $tasks, $argv[2]);
            } else {
                echo "Need 'id' \n";
            }
            break;
        case "mark-done":
            if (isset($argv[2])) {
                markDone($file, $tasks, $argv[2]);
            } else {
                echo "Need 'id' \n";
            }
            break;
        case "help":
            helper();
            break;
        default:
            helper();
            break;
    }
} else {
    helper();
}

function addTask(mixed $file, array &$tasks, ?string $description): void
{
    if ($description || strlen(trim($description)) > 0) {
        $tasks[] = [
            'description' => $description,
            'status'      => "todo",
            'created_at'  => date('d.m.Y H:i:s'),
            'updated_at'  => ""
        ];
        $handle = file_put_contents($file, json_encode($tasks));
        if ($handle) {
            echo "Written {$handle} bytes";
        } else {
            echo "Failed to write";
        }
    }else {
        echo "Missing description argument";
    }
    
}
function updateTask(mixed $file, array &$tasks, ?int $id, ?string $description): void
{
    if (!count($tasks)) {
        echo "No tasks available\n";
    } elseif (!$id && $id !== 0) {
        echo "Missing argument item id\n";
    } elseif (!isset($tasks[$id])) {
        echo "Item does not exist\n";
    } elseif (!strlen(trim($description))) {
        echo "Missing argument item new description\n";
    } else {
        $tasks[$id] = [
            'description' => $description,
            'status'      => $tasks[$id]['status'],
            'created_at'  => $tasks[$id]['created_at'],
            'updated_at'  => date('d.m.Y H:i:s')
        ];
        $handle = file_put_contents($file, json_encode($tasks));
        if ($handle) {
            echo "Written {$handle} bytes\n";
        } else {
            echo "Failed to write\n";
        }
    }
}
function removeTask(mixed $file, array &$tasks, ?int $id): void
{
    if (!count($tasks)) {
        echo "No tasks available\n";
    } elseif (!$id && $id !== 0) {
        echo "Missing argument item id\n";
    } elseif (!isset($tasks[$id])) {
        echo "Item does not exist\n";
    } else {
        unset($tasks[$id]);
        $handle = file_put_contents($file, json_encode($tasks));
        if ($handle) {
            echo "Written {$handle} bytes\n";
            echo "Removed task with {$id}\n";
        } else {
            echo "Failed to write\n";
        }
    }
}
function markInProgress(mixed $file, array &$tasks, ?int $id): void
{
    if (!count($tasks)) {
        echo "No tasks available\n";
    } elseif (!$id && $id !== 0) {
        echo "Missing argument item id\n";
    } elseif (!isset($tasks[$id])) {
        echo "Item does not exist\n";
    } else {
        $tasks[$id] = [
            'description' => $tasks[$id]['description'],
            'status'      => "in_progress",
            'created_at'  => $tasks[$id]['created_at'],
            'updated_at'  => date('d.m.Y H:i:s')
        ];
        $handle = file_put_contents($file, json_encode($tasks));
        if ($handle) {
            echo "Written {$handle} bytes\n";
        } else {
            echo "Failed to write\n";
        }
    }
}
function markDone(mixed $file, array &$tasks, ?int $id): void
{
    if (!count($tasks)) {
        echo "No tasks available\n";
    } elseif (!$id && $id !== 0) {
        echo "Missing argument item id\n";
    } elseif (!isset($tasks[$id])) {
        echo "Item does not exist\n";
    } else {
        $tasks[$id] = [
            'description' => $tasks[$id]['description'],
            'status'      => "done",
            'created_at'  => $tasks[$id]['created_at'],
            'updated_at'  => date('d.m.Y H:i:s')
        ];
        $handle = file_put_contents($file, json_encode($tasks));
        if ($handle) {
            echo "Written {$handle} bytes\n";
        } else {
            echo "Failed to write\n";
        }
    }
}
function viewAll(array &$tasks): void
{
    if (!count($tasks)) {
        echo "No tasks available\n";
    } else {
        var_dump($tasks);die;
        foreach ($tasks as $id => $task) {
            listContent($task, $id);
            echo "\n";
        }
    }
}
function viewByStatus(array &$tasks, string $status): void
{
    if(!count($tasks)) {
        echo "No tasks available\n";
    } else {
        $arr = array_filter($tasks, function ($a) use ($status) {
            return $a['status'] === $status;
        });
        foreach ($arr as $k => $v) {
            listContent($v, $k);
        } 
    }
}
function listContent(array $task, int $id): void
{
    echo "Id: {$id}\n";
    echo "Description: {$task['description']}\n";
    echo "Status: " . $task['status'] . "\n";
    echo "Created at: {$task['created_at']}\n";
    echo "Updated at: {$task['updated_at']}\n\r";
}
function helper(): void
{
    echo "Available commands:\n
    \"add\" - with argument of a description use double quotes \"\"\n
    \"update\" - with argument number \"id\" and another argument description with double quotes \"\"\n
    \"delete\" - with argument number \"id\"\n
    \"list\" - use either \"todo\", \"in_progress\" or \"done\" to list tasks by status, or no status to list all\n
    \"mark-in-progress\": with argument number \"id\" to set task's status to in-progress \n
    \"mark-done\": with argument number \"id\" to set task's status to done \n";
}
?>