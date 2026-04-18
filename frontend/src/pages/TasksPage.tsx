import { useEffect, useState } from 'react'
import type { Task, TaskList, TaskType } from '../interface/Task'
import { getAdminTasks, createAdminTask, completeAdminTask } from '../api/api'
import './style/TasksPage.css'

const TYPE_LABEL: Record<TaskType, string> = {
  feature: 'Feature',
  bug: 'Bug',
  maintenance: 'Maintenance',
}

export const TasksPage: React.FC = () => {
  const [data, setData] = useState<TaskList>({ open: [], done: [] })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [type, setType] = useState<TaskType>('feature')
  const [submitting, setSubmitting] = useState(false)

  const load = () => {
    setLoading(true)
    getAdminTasks()
      .then((d) => setData(d))
      .catch((e: Error) => setError(e.message))
      .finally(() => setLoading(false))
  }

  useEffect(() => { load() }, [])

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!title.trim() || submitting) return
    setSubmitting(true)
    createAdminTask(title.trim(), description.trim() || null, type)
      .then(() => {
        setTitle('')
        setDescription('')
        setType('feature')
        load()
      })
      .catch((err: Error) => setError(err.message))
      .finally(() => setSubmitting(false))
  }

  const markDone = (id: number) => {
    completeAdminTask(id).then(load).catch((e: Error) => setError(e.message))
  }

  return (
    <div className="tasks-page">
      <header className="tasks-header">
        <h1>Admin Tasks</h1>
        <p className="tasks-sub">Private in-app backlog — feature ideas, bugs, maintenance.</p>
      </header>

      <form className="task-form" onSubmit={submit}>
        <input
          className="task-input"
          placeholder="Title"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          required
          maxLength={200}
        />
        <textarea
          className="task-textarea"
          placeholder="Description (optional)"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
          rows={3}
        />
        <div className="task-form-row">
          <select className="task-select" value={type} onChange={(e) => setType(e.target.value as TaskType)}>
            <option value="feature">Feature</option>
            <option value="bug">Bug</option>
            <option value="maintenance">Maintenance</option>
          </select>
          <button className="task-submit" type="submit" disabled={submitting || !title.trim()}>
            {submitting ? 'Adding…' : 'Add Task'}
          </button>
        </div>
      </form>

      {error && <div className="task-error">{error}</div>}

      {loading ? (
        <p className="task-meta">Loading…</p>
      ) : (
        <>
          <section className="task-section">
            <h2>Open <span className="task-count">{data.open.length}</span></h2>
            {data.open.length === 0 && <p className="task-meta">Nothing open — nice.</p>}
            <ul className="task-list">
              {data.open.map((t) => (
                <TaskRow key={t.id} task={t} onComplete={markDone} />
              ))}
            </ul>
          </section>

          <section className="task-section">
            <h2>Done <span className="task-count">{data.done.length}</span></h2>
            {data.done.length === 0 && <p className="task-meta">No completed tasks yet.</p>}
            <ul className="task-list">
              {data.done.map((t) => (
                <TaskRow key={t.id} task={t} />
              ))}
            </ul>
          </section>
        </>
      )}
    </div>
  )
}

const TaskRow: React.FC<{ task: Task; onComplete?: (id: number) => void }> = ({ task, onComplete }) => {
  const creator = task.created_by_username ?? 'unknown'
  return (
    <li className={`task-row task-row-${task.status}`}>
      <div className="task-row-main">
        <div className="task-row-top">
          <span className={`task-type task-type-${task.type}`}>{TYPE_LABEL[task.type]}</span>
          <span className="task-title">{task.title}</span>
        </div>
        {task.description && <p className="task-desc">{task.description}</p>}
        <div className="task-row-meta">
          <span>by {creator}</span>
          <span>· {new Date(task.created_at).toLocaleDateString()}</span>
          {task.completed_at && <span>· done {new Date(task.completed_at).toLocaleDateString()}</span>}
        </div>
      </div>
      {onComplete && (
        <button className="task-complete" onClick={() => onComplete(task.id)}>Complete</button>
      )}
    </li>
  )
}
