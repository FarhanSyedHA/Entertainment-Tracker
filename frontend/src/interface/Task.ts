export type TaskType = 'feature' | 'bug' | 'maintenance'
export type TaskStatus = 'open' | 'done'

export interface Task {
  id: number
  title: string
  description: string | null
  type: TaskType
  status: TaskStatus
  created_at: string
  completed_at: string | null
  created_by: number | null
  created_by_username: string | null
}

export interface TaskList {
  open: Task[]
  done: Task[]
}
