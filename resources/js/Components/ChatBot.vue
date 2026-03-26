<template>
  <div class="chatbot-root" role="dialog" aria-label="Help assistant">
    <div class="chat-header">
      <div>Assistant</div>
    </div>

    <div class="chat-choices">
      <button class="choice" @click.prevent="choose('add_account')">Add Bank Account</button>
      <button class="choice" @click.prevent="choose('create_transaction')">Create Transaction</button>
      <button class="choice" @click.prevent="choose('manage_categories')">Manage Categories</button>
      <button class="choice" @click.prevent="choose('export_csv')">Export CSV</button>
      <button class="choice" @click.prevent="choose('navigation')">App Navigation Tour</button>
    </div>

    <div class="chat-messages" ref="messagesRef">
      <div v-for="(m, i) in messages" :key="i" :class="['chat-message', m.from]">
        <div class="chat-text">{{ m.text }}</div>
      </div>
    </div>

    <form class="chat-input" @submit.prevent="send">
      <input
        v-model="input"
        :placeholder="loading ? 'Thinking...' : ''"
        @keydown.enter.exact.prevent="send"
        :disabled="loading"
      />
      <button type="submit" class="btn btn-sm btn-primary" :disabled="loading || !input.trim()">Send</button>
    </form>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'

const messages = ref([
  { from: 'ai', text: 'Hi — I can help you use sTracker. Choose one of the quick topics below to get step-by-step guidance.' }
])
const input = ref('')
const loading = ref(false)
const messagesRef = ref(null)

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}

async function typeWrite(text, msgIndex, speed = 20) {
  if (!text || typeof text !== 'string') {
    messages.value[msgIndex].text = String(text || '')
    return
  }
  messages.value[msgIndex].text = ''
  for (let i = 0; i < text.length; i++) {
    messages.value[msgIndex].text += text[i]
    scrollBottom()
    // small pause to simulate typing
    // slight longer pause on punctuation for realism
    const ch = text[i]
    const extra = /[.,!?]/.test(ch) ? speed * 6 : 0
    await sleep(speed + extra)
  }
}

function scrollBottom() {
  nextTick(() => {
    try { messagesRef.value.scrollTop = messagesRef.value.scrollHeight } catch (e) {}
  })
}

async function send() {
  const text = input.value.trim()
  if (!text) return
  messages.value.push({ from: 'user', text })
  input.value = ''
  loading.value = true
  messages.value.push({ from: 'ai', text: '...' })
  scrollBottom()

  try {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]')
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : ''

    const res = await fetch('/ai-chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ message: text })
    })

    if (!res.ok) {
      const err = await res.json().catch(() => null)
      throw new Error(err?.message || 'Error from server')
    }

    const data = await res.json()
    // replace the last ai placeholder with a typewriter effect
    for (let i = messages.value.length - 1; i >= 0; i--) {
      if (messages.value[i].from === 'ai') {
        await typeWrite(data.message, i)
        break
      }
    }
  } catch (e) {
    for (let i = messages.value.length - 1; i >= 0; i--) {
      if (messages.value[i].from === 'ai') {
        await typeWrite('Sorry, I could not fetch a response.', i)
        break
      }
    }
  } finally {
    loading.value = false
    scrollBottom()
  }
}

function choose(key) {
  const choice = choices.find(c => c.key === key)
  if (!choice) return
  messages.value.push({ from: 'user', text: choice.label })
  // Compose reply from steps
  const reply = choice.steps.join('\n')
  // push placeholder ai message and type it out
  messages.value.push({ from: 'ai', text: '...' })
  scrollBottom()
  const aiIndex = messages.value.length - 1
  // fire-and-forget typing (no loading state for quick choices)
  typeWrite(reply, aiIndex, 16)
}

const choices = [
  {
    key: 'add_account',
    label: 'Add Bank Account',
    steps: [
      'Open the Bank Accounts page (sidebar → Bank Accounts).',
      'Click "Add Bank Account" (top-right or bottom on mobile).',
      'Fill in the account name and starting balance, then click Save.',
      'Use the Edit button on a card to change details or Delete to remove it.'
    ]
  },
  {
    key: 'create_transaction',
    label: 'Create Transaction',
    steps: [
      'Go to Transactions → Add or click the New Transaction button.',
      'Select the bank account, choose a category, enter amount and date.',
      'Optionally add a note and save. Use the Recent Transactions page to filter and find it.'
    ]
  },
  {
    key: 'manage_categories',
    label: 'Manage Categories',
    steps: [
      'Open the Categories page from the sidebar.',
      'Click Add to create a new category; edit or delete existing categories with their buttons.',
      'Use categories when creating transactions to group expenses.'
    ]
  },
  {
    key: 'export_csv',
    label: 'Export CSV',
    steps: [
      'On Recent Transactions page, apply any filters you need (date, account, category).',
      'Click Export CSV to download the filtered transactions as a CSV file.'
    ]
  },
  {
    key: 'navigation',
    label: 'App Navigation Tour',
    steps: [
      'The left sidebar contains: Dashboard, Bank Accounts, Transactions, Categories, and Profile.',
      'Top-right contains settings and theme toggle. Use the mascot for help anytime.'
    ]
  }
]

scrollBottom()
</script>

<style scoped>
.chatbot-root {
  width: min(340px, 92vw);
  max-width: 420px;
  background: rgba(255,255,255,0.96);
  color: #0f172a;
  border-radius: 12px;
  box-shadow: 0 12px 30px rgba(2,6,23,0.14);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.chat-header {
  font-weight: 700;
  padding: 0.6rem 0.9rem;
  background: linear-gradient(90deg,#7c3aed,#f59e0b);
  color: white;
  display:flex;
  align-items:center;
}
.chat-choices { display:flex; flex-wrap:wrap; gap:0.5rem; padding:0.6rem }
.choice { background: rgba(0,0,0,0.04); border:0; padding:0.38rem 0.6rem; border-radius:9999px; font-size:0.85rem; cursor:pointer }
.choice:hover { transform: translateY(-2px) }
.chat-messages { max-height: 260px; overflow:auto; padding: 0.4rem 0.6rem; flex:1 }
.chat-message { margin-bottom: 0.6rem; display:flex }
.chat-message.user { justify-content: flex-end }
.chat-message.ai { justify-content: flex-start }
.chat-text { padding: 0.5rem 0.68rem; border-radius: 10px; max-width: 80%; line-height:1.25; white-space:pre-wrap }
.chat-message.user .chat-text {
  background: linear-gradient(90deg,#4f46e5,#7c3aed); /* stronger primary gradient for user messages */
  color: #ffffff;
}
.chat-message.ai .chat-text {
  background: linear-gradient(90deg,#f1f5f9,#e2e8f0); /* light neutral for assistant */
  color: #0f172a;
}
.chat-input { display:flex; gap:0.5rem; padding: 0.5rem; border-top: 1px solid rgba(2,6,23,0.05) }
.chat-input input { flex:1; padding:0.45rem 0.6rem; border-radius:8px; border:1px solid #e6e6e9 }

@media (prefers-color-scheme: dark) {
  .chatbot-root { background: rgba(8,10,18,0.9); color: #e6eef8 }
  .chat-text { color: #e6eef8 }
  .choice { background: rgba(255,255,255,0.03) }
  .chat-message.user .chat-text {
    background: linear-gradient(90deg,#3730a3,#7c3aed);
    color: #ffffff;
  }
  .chat-message.ai .chat-text {
    background: linear-gradient(90deg,#0b1220,#172554);
    color: #e6eef8;
  }
  .chat-input input { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); color: #e6eef8 }
}
</style>
