export type Project = {
    id: number;
    user_id: number;
    name: string;
    description: string | null;
    created_at: string;
    updated_at: string;
};

export type Priority = 'low' | 'medium' | 'high';

export type Status = 'todo' | 'in_progress' | 'completed';

export type Task = {
    id: number;
    project_id: number;
    title: string;
    description: string | null;
    priority: Priority;
    status: Status;
    due_date: string | null;
    created_at: string;
    updated_at: string;
};
