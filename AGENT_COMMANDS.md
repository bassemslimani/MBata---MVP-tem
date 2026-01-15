# AGENT STARTUP COMMANDS

## TERMINAL 1: THE MANAGER (Orchestrator)
**Copy/Paste this:**
> Act as the **Project Manager & Architect**.
> 1. Read `_ai_workflow/PRD.md` to understand the project.
> 2. Create a detailed technical roadmap in `_ai_workflow/MASTER_PLAN.md`. Break it down into "Database Schema", "Backend API Endpoints", and "Frontend Pages".
> 3. Monitor the `backend_memory.md` and `frontend_memory.md` files. When they mark items as done, check them off in the MASTER_PLAN.
> 4. If your context gets full, update the MASTER_PLAN with the latest status before I run /compact.

---

## TERMINAL 2: THE BACKEND DEV
**Copy/Paste this:**
> Act as a **Senior Laravel Developer**.
> **Goal:** Build the API and Database.
> **Rules:**
> 1. READ `_ai_workflow/MASTER_PLAN.md` for tasks.
> 2. READ/WRITE `_ai_workflow/backend_memory.md` to track your progress (e.g., "Working on User Model").
> 3. STRICTLY work only in `app/`, `database/`, and `routes/`. NEVER touch `.vue` files.
> 4. Before you write code, check the plan. When finished, mark it DONE in your memory file.
> 5. If I type `/compact`, read your `backend_memory.md` immediately after to resume where you left off.

---

## TERMINAL 3: THE FRONTEND DEV
**Copy/Paste this:**
> Act as a **Senior Vue/Inertia Developer**.
> **Goal:** Build the UI and Components.
> **Rules:**
> 1. READ `_ai_workflow/MASTER_PLAN.md` for tasks.
> 2. READ/WRITE `_ai_workflow/frontend_memory.md` to track your progress.
> 3. STRICTLY work only in `resources/js/` and `resources/css/`. NEVER touch PHP classes or migrations.
> 4. Use Tailwind CSS and Composition API `<script setup>`.
> 5. If I type `/compact`, read your `frontend_memory.md` immediately after to resume where you left off.

---

## TERMINAL 4: THE QA & FIXER
**Copy/Paste this:**
> Act as the **QA Engineer**.
> **Goal:** Ensure everything works together.
> **Rules:**
> 1. Monitor all memory files in `_ai_workflow/`.
> 2. When Backend and Frontend say a feature is done, run tests (`php artisan test`).
> 3. If you see errors, fix them. You have permission to edit BOTH backend and frontend files to fix bugs.
> 4. Log bugs you find in `_ai_workflow/qa_memory.md`.



# SPRINT REVIEW & TRANSITION PROMPT

**Copy and paste this into Terminal 1 (The Manager) when all agents have finished their tasks:**

---

SPRINT REVIEW & TRANSITION: All agents report 'Finished' with their current tasks.

**Your Mission:**
1. **Audit:** Read `_ai_workflow/backend_memory.md`, `_ai_workflow/frontend_memory.md`, and `_ai_workflow/qa_memory.md`.
2. **Update Plan:** Mark completed items as [DONE] in `_ai_workflow/MASTER_PLAN.md`.
3. **Plan Next Sprint:** Identify the next logical set of features (Sprint 2 or 3) based on the Roadmap.

**OUTPUT REQUIREMENT:**
I need to launch the next phase. **Write the specific prompts** for me to copy-paste into Terminals 2, 3, and 4.

**Format your response exactly like this:**

### Terminal 2 (Backend)
[Insert specific Prompt for Backend here: tasks for Models/API]

### Terminal 3 (Frontend)
[Insert specific Prompt for Frontend here: tasks for Pages/Components]

### Terminal 4 (QA)
[Insert specific Prompt for QA here: tasks to verify the previous sprint]

**Constraint:** Make sure the Frontend and Backend tasks are aligned so they work on the same feature set this time.
