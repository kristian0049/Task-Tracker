<?php
class TasksTracker
{
    private $argv;
    private array $tasks = [];
    private string $file = "";
    public function __construct(string $fileName, $argv)
    {
        $this->file = $fileName;
        $this->argv = $argv;
        $tasks = file_get_contents($fileName);
        if ($tasks) {
            $this->tasks = json_decode($tasks, true);
        } else {
            $this->tasks = [];
        }
    }
    private function addTask(?string $description): void
    {
        if ($description || strlen(trim($description)) > 0) {
            $this->tasks[] = [
                'description' => $description,
                'status'      => "todo",
                'created_at'  => date('d.m.Y H:i:s'),
                'updated_at'  => ""
            ];
            $handle = file_put_contents($this->file, json_encode($this->tasks));
            if ($handle) {
                echo "Written {$handle} bytes";
            } else {
                echo "Failed to write";
            }
        }else {
            echo "Missing description argument";
        }
        
    }
    private function updateTask(?int $id, ?string $description): void
    {
        if (!count($this->tasks)) {
            echo "No tasks available\n";
        } elseif (!$id && $id !== 0) {
            echo "Missing argument item id\n";
        } elseif (!isset($this->tasks[$id])) {
            echo "Item does not exist\n";
        } elseif (!strlen(trim($description))) {
            echo "Missing argument item new description\n";
        } else {
            $this->tasks[$id] = [
                'description' => $description,
                'status'      => $this->tasks[$id]['status'],
                'created_at'  => $this->tasks[$id]['created_at'],
                'updated_at'  => date('d.m.Y H:i:s')
            ];
            $handle = file_put_contents($this->file, json_encode($this->tasks));
            if ($handle) {
                echo "Written {$handle} bytes\n";
            } else {
                echo "Failed to write\n";
            }
        }
    }
    private function removeTask(?int $id): void
    {
        if (!count($this->tasks)) {
            echo "No tasks available\n";
        } elseif (!$id && $id !== 0) {
            echo "Missing argument item id\n";
        } elseif (!isset($this->tasks[$id])) {
            echo "Item does not exist\n";
        } else {
            unset($this->tasks[$id]);
            $handle = file_put_contents($this->file, json_encode($this->tasks));
            if ($handle) {
                echo "Written {$handle} bytes\n";
                echo "Removed task with {$id}\n";
            } else {
                echo "Failed to write\n";
            }
        }
    }
    private function markInProgress(?int $id): void
    {
        if (!count($this->tasks)) {
            echo "No tasks available\n";
        } elseif (!$id && $id !== 0) {
            echo "Missing argument item id\n";
        } elseif (!isset($this->tasks[$id])) {
            echo "Item does not exist\n";
        } else {
            $this->tasks[$id] = [
                'description' => $this->tasks[$id]['description'],
                'status'      => "in_progress",
                'created_at'  => $this->tasks[$id]['created_at'],
                'updated_at'  => date('d.m.Y H:i:s')
            ];
            $handle = file_put_contents($this->file, json_encode($this->tasks));
            if ($handle) {
                echo "Written {$handle} bytes\n";
            } else {
                echo "Failed to write\n";
            }
        }
    }
    private function markDone(?int $id): void
    {
        if (!count($this->tasks)) {
            echo "No tasks available\n";
        } elseif (!$id && $id !== 0) {
            echo "Missing argument item id\n";
        } elseif (!isset($this->tasks[$id])) {
            echo "Item does not exist\n";
        } else {
            $this->tasks[$id] = [
                'description' => $this->tas[$id]['description'],
                'status'      => "done",
                'created_at'  => $this->tas[$id]['created_at'],
                'updated_at'  => date('d.m.Y H:i:s')
            ];
            $handle = file_put_contents($this->file, json_encode($this->tasks));
            if ($handle) {
                echo "Written {$handle} bytes\n";
            } else {
                echo "Failed to write\n";
            }
        }
    }
    private function viewAll(): void
    {
        if (!count($this->tasks)) {
            echo "No tasks available\n";
        } else {
            foreach ($this->tasks as $id => $task) {
                $this->listContent($task, $id);
                echo "\n";
            }
        }
    }
    private function viewByStatus(string $status): void
    {
        if(!count($this->tasks)) {
            echo "No tasks available\n";
        } else {
            $arr = array_filter($this->tasks, function ($a) use ($status) {
                return $a['status'] === $status;
            });
            foreach ($arr as $k => $v) {
                $this->listContent($v, $k);
            } 
        }
    }
    private function listContent(array $task, int $id): void
    {
        echo "Id: {$id}\n";
        echo "Description: {$task['description']}\n";
        echo "Status: " . $task['status'] . "\n";
        echo "Created at: {$task['created_at']}\n";
        echo "Updated at: {$task['updated_at']}\n\r";
    }
    private function helper(): void
    {
        echo "Available commands:\n
        \"add\" - with argument of a description use double quotes \"\"\n
        \"update\" - with argument number \"id\" and another argument description with double quotes \"\"\n
        \"delete\" - with argument number \"id\"\n
        \"list\" - use either \"todo\", \"in_progress\" or \"done\" to list tasks by status, or no status to list all\n
        \"mark-in-progress\": with argument number \"id\" to set task's status to in-progress \n
        \"mark-done\": with argument number \"id\" to set task's status to done \n";
    }
    public function core(): void
    {
        if(isset($this->argv[1]) && strlen(trim($this->argv[1]))) {
            switch ($this->argv[1]) {
                case "add":
                    if (isset($this->argv[2])) {
                        $this->addTask($this->argv[2]);
                    } else {
                        echo "No description added\n";
                    }
                    break;
                case "update":
                    if (isset($this->argv[2]) && isset($this->argv[3])) {
                        $this->updateTask($this->argv[2], $this->argv[3]);
                    } else {
                        echo "Need argument 'id' followed by 'description'\n";
                    }
                    break;
                case "delete":
                    if (isset($this->argv[2])) {
                        $this->removeTask($this->argv[2]);
                    } else {
                        echo "Need 'id' \n";
                    }
                    break;
                case "list":
                    if (isset($this->argv[2]) && strlen(trim($this->argv[2]))){
                        $this->viewByStatus($this->argv[2]);
                    } else {
                        $this->viewAll();
                    }
                    break;
                case "mark-in-progress":
                    if (isset($this->argv[2])) {
                        $this->markInProgress($this->argv[2]);
                    } else {
                        echo "Need 'id' \n";
                    }
                    break;
                case "mark-done":
                    if (isset($this->argv[2])) {
                        $this->markDone($this->argv[2]);
                    } else {
                        echo "Need 'id' \n";
                    }
                    break;
                case "help":
                    $this->helper();
                    break;
                default:
                    $this->helper();
                    break;
            }
        } else {
            $this->helper();
        }
    } 
}
$tasks = new TasksTracker('tasks.json', $argv);
$tasks->core();
?>