export interface Collection {
    id: number;
    name: string;
    access: string;
    created_at: string;
    stampCount?: number;
    authorName?: string;
    stamps?: Stamp[];
    userAccess?: 'owner' | 'member' | 'public' | 'none';
    members: CollectionMember[];
}

export interface CollectionMember {
    user_id: number;
    collection_id: number;
    username: string;
    role: string;
}

export interface Stamp {
    id: number;
    collection_id: number;
    name: string;
    used: boolean;
    damaged: boolean;
    created_at: string;
    collection?: Collection;
    headerUrl?: string;
}

export interface User {
    id: number;
    username: string;
    first_name: string;
    last_name: string;
    email: string;
    created_at: string;
}
