export type UserRole = 'super_admin' | 'company_admin' | 'manager' | 'employee'
export type WorkflowStatus = 'active' | 'paused' | 'draft'
export type AutomationStatus = 'running' | 'completed' | 'failed' | 'paused'
export type NotificationType = 'info' | 'warning' | 'error' | 'success'
export type OrgPlan = 'starter' | 'professional' | 'enterprise'

export interface User {
  id: string
  email: string
  name: string
  role: UserRole
  organizationId: string | null
  avatarUrl?: string | null
  createdAt: string
  organization?: Organization | null
}

export interface Organization {
  id: string
  name: string
  slug: string
  plan: OrgPlan
  logoUrl?: string | null
  createdAt?: string
}

export interface Workflow {
  id: string
  name: string
  description: string | null
  status: WorkflowStatus
  trigger: string
  trigger_config?: Record<string, unknown> | null
  organizationId?: string
  organization_id?: string
  created_by?: string
  creator?: { id: string; name: string; avatar_url?: string | null } | null
  runCount?: number
  run_count?: number
  lastRunAt?: string | null
  last_run_at?: string | null
  createdAt?: string
  created_at?: string
  updatedAt?: string
  updated_at?: string
}

export interface Automation {
  id: string
  name: string
  description: string | null
  workflowId: string | null
  workflow_id: string | null
  workflow?: { id: string; name: string; status: WorkflowStatus } | null
  status: AutomationStatus
  triggerType: string
  trigger_type: string
  actionType: string
  action_type: string
  config?: Record<string, unknown> | null
  isActive: boolean
  is_active: boolean
  lastExecutedAt?: string | null
  last_executed_at?: string | null
  executionCount?: number
  execution_count?: number
  createdAt?: string
  created_at?: string
}

export interface AiLog {
  id: string
  userId?: string
  prompt: string
  response: string
  model: string
  tokensUsed: number
  createdAt: string
}

export interface AppNotification {
  id: string
  userId?: string
  title: string
  message: string
  type: NotificationType
  read: boolean
  createdAt: string
}

export interface DashboardStats {
  kpis: {
    workflows: { total: number; active: number }
    automations: { total: number; active: number }
    aiQueries: { thisMonth: number }
    executions: { total: number }
  }
  recentActivity: ActivityItem[]
}

export interface ActivityItem {
  id: string
  type: 'workflow' | 'automation' | 'ai' | 'system'
  title: string
  time: string
  status?: string
}

export interface AnalyticsOverview {
  totalWorkflows: number
  activeWorkflows: number
  totalAutomations: number
  automationExecutions: number
  aiQueries: number
  tokensUsed: number
}

export interface ActivityDataPoint {
  date: string
  workflows: number
  automations: number
  aiQueries: number
}

export interface ApiResponse<T> {
  data: T
  message?: string
  meta?: {
    total: number
    page: number
    perPage: number
    unread?: number
  }
}

export interface ChatMessage {
  id: string
  role: 'user' | 'assistant'
  content: string
  timestamp: Date
  isLoading?: boolean
}

export interface CreateWorkflowPayload {
  name: string
  description?: string
  trigger: string
  trigger_config?: Record<string, unknown>
  status?: WorkflowStatus
}

export interface CreateAutomationPayload {
  name: string
  description?: string
  workflow_id?: string | null
  trigger_type: string
  action_type: string
  config?: Record<string, unknown>
  is_active?: boolean
}
