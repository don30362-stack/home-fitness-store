export interface User {
    id: number
    name: string
    email: string
    phone: string
    status: string
}

export interface LoginPayload {
    email: string
    password: string
}

export interface RegisterPayload {
    name: string
    email: string
    phone: string
    password: string
    password_confirmation: string
}

export interface UpdateProfilePayload {
    name: string
    email: string
    phone: string
}

export interface UpdatePasswordPayload {
    current_password: string
    password: string
    password_confirmation: string
}