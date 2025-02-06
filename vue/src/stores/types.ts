export interface Collection {
    id: number;
    name: string;
    access: string;
    created_at: string;
    stampCount?: number;
    authorName?: string;
}

export interface User {
    id: number;
    username: string;
    first_name: string;
    last_name: string;
    email: string;
    created_at: string;
}
