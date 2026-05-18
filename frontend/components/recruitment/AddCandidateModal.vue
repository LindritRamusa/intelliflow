<script setup lang="ts">
import type { CreateCandidatePayload } from '~/types'

const emit = defineEmits<{
  close: []
  submit: [payload: CreateCandidatePayload]
}>()

const isSubmitting = ref(false)

const form = reactive<CreateCandidatePayload>({
  name: '',
  email: '',
  phone: '',
  applied_role: '',
  cv_text: '',
  source: '',
  notes: '',
  skills: [],
})

const skillInput = ref('')

const handleAddSkill = () => {
  const skill = skillInput.value.trim()
  if (skill && !form.skills?.includes(skill)) {
    form.skills = [...(form.skills ?? []), skill]
    skillInput.value = ''
  }
}

const handleRemoveSkill = (skill: string) => {
  form.skills = form.skills?.filter(s => s !== skill) ?? []
}

const handleSkillKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault()
    handleAddSkill()
  }
}

const handleSubmit = async () => {
  if (!form.name.trim() || !form.applied_role.trim()) return
  isSubmitting.value = true
  try {
    emit('submit', { ...form })
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-[#141824] border border-white/10 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
      <div class="flex items-center justify-between p-6 border-b border-white/[0.06]">
        <div>
          <h2 class="text-white font-semibold text-lg">Add Candidate</h2>
          <p class="text-gray-400 text-sm mt-0.5">Add a new candidate for AI-powered screening</p>
        </div>
        <button class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-colors" @click="emit('close')">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="p-6 space-y-5" @submit.prevent="handleSubmit">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Full Name <span class="text-red-400">*</span></label>
            <input
              v-model="form.name"
              type="text"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
              placeholder="John Doe"
              required
            />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Applied Role <span class="text-red-400">*</span></label>
            <input
              v-model="form.applied_role"
              type="text"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
              placeholder="Senior Software Engineer"
              required
            />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
              placeholder="john@example.com"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Phone</label>
            <input
              v-model="form.phone"
              type="tel"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
              placeholder="+1 555 000 0000"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-300 mb-1.5">CV / Resume Text</label>
          <p class="text-xs text-gray-500 mb-2">Paste the CV text below for AI-powered analysis and scoring</p>
          <textarea
            v-model="form.cv_text"
            rows="6"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors resize-none"
            placeholder="Paste the candidate's CV text here..."
          />
        </div>

        <div>
          <label class="block text-sm text-gray-300 mb-1.5">Skills</label>
          <div class="flex flex-wrap gap-1.5 mb-2">
            <span
              v-for="skill in form.skills"
              :key="skill"
              class="flex items-center gap-1 px-2 py-1 bg-violet-500/15 border border-violet-500/20 rounded-full text-xs text-violet-300"
            >
              {{ skill }}
              <button type="button" class="hover:text-white transition-colors" @click="handleRemoveSkill(skill)">×</button>
            </span>
          </div>
          <input
            v-model="skillInput"
            type="text"
            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
            placeholder="Type a skill and press Enter"
            @keydown="handleSkillKeydown"
            @blur="handleAddSkill"
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Source</label>
            <select
              v-model="form.source"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-violet-500/50 transition-colors"
            >
              <option value="" class="bg-[#1a1f2e]">Select source</option>
              <option value="LinkedIn" class="bg-[#1a1f2e]">LinkedIn</option>
              <option value="Referral" class="bg-[#1a1f2e]">Referral</option>
              <option value="Indeed" class="bg-[#1a1f2e]">Indeed</option>
              <option value="Company Website" class="bg-[#1a1f2e]">Company Website</option>
              <option value="Other" class="bg-[#1a1f2e]">Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-300 mb-1.5">Notes</label>
            <input
              v-model="form.notes"
              type="text"
              class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm placeholder-gray-500 focus:outline-none focus:border-violet-500/50 focus:bg-white/[0.07] transition-colors"
              placeholder="Internal notes..."
            />
          </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button
            type="button"
            class="flex-1 px-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-gray-300 text-sm hover:bg-white/[0.08] transition-colors"
            @click="emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isSubmitting || !form.name.trim() || !form.applied_role.trim()"
            class="flex-1 px-4 py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg text-white text-sm font-medium transition-colors"
          >
            {{ isSubmitting ? 'Adding...' : 'Add Candidate' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
