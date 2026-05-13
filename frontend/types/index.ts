export type UserRole = 'super_admin' | 'company_admin' | 'manager' | 'employee'

export interface User {
  id: string
  email: string
  name: string
  role: UserRole
  organizationId: string
  avatarUrl?: string
  createdAt: string
}

export interface Organization {
  id: string
  name: string
  slug: string
  plan: 'starter' | 'professional' | 'enterprise'
  logoUrl?: string
  createdAt: string
}

export interface Workflow {
  id: string
  name: string
  description: string
  status: 'active' | 'paused' | 'draft'
  trigger: string
  organizationId: string
  createdAt: string
  updatedAt: string
}

export interface Automation {
  id: string
  name: string
  workflowId: string
  status: 'running' | 'completed' | 'failed'
  executedAt: string
}

export interface AiLog {
  id: string
  userId: string
  prompt: string
  response: string
  model: string
  tokensUsed: number
  createdAt: string
}

export interface Notification {
  id: string
  userId: string
  title: string
  message: string
  type: 'info' | 'warning' | 'error' | 'success'
  read: boolean
  createdAt: string
}

export interface ApiResponse<T> {
  data: T
  message?: string
  meta?: {
    total: number
    page: number
    perPage: number
  }
}
