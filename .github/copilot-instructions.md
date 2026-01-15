# Copilot Instructions - HIIT Training App

## Project Overview
This is a **Progressive Web App (PWA)** for HIIT workout training with 30-minute routines. It runs on a local MAMP server with a three-tier data persistence system: Firebase → Physical Files → localStorage.

**Key Stack**: HTML/JavaScript (frontend), PHP (backend API), JSON (data storage)

## Architecture Essentials

### Data Persistence Strategy (Critical)
The app uses a **three-tier fallback system** defined in `api/data_handler.php`:
1. **Firebase** (if available)
2. **Physical files** on server (`data/users/[user-id]/`)
3. **localStorage** (browser fallback)

User data structure:
```
data/users/[user-id]/
├── progress/current_progress.json        # Active workout state
├── history/[YYYY-MM-DD].json            # Completed workouts by date
└── backups/progress_[timestamp].json     # Auto-created before overwrites
```

**Backup retention**: 30 days (automatic cleanup in `cleanOldBackups()`)

### PWA Architecture
- **Service Worker**: `sw.js` handles offline caching, background sync, push notifications
- **Manifest**: `manifest.json` provides app metadata, shortcuts, and icon definitions
- **Caching strategy**: Network-first for API calls, cache-first for static assets

### User ID System
Three ID types (auto-generated in `index.html`):
- **Custom** (recommended): `juan-2025`, `maria-fitness` - created via UI input
- **Firebase**: Random alphanumeric UUID from Firebase auth
- **Local**: `user-[random]` format as fallback

ID persistence uses localStorage key: `hiitTrainer_userId`

## API Endpoints & Backend

### PHP Classes & Methods
**`WorkoutDataHandler` class** (`api/data_handler.php`):
- `saveProgress($data)` - Saves current_progress.json, creates backup
- `loadProgress()` - Retrieves active workout state
- `saveToHistory($workoutData)` - Archives completed workouts to date-based files
- `loadHistory($date)` - Retrieves specific day's workout (format: `Y-m-d`)
- `cleanOldBackups()` - Removes backups older than 30 days
- `sanitizeUserId($userId)` - Prevents directory traversal (regex: `[^a-zA-Z0-9\-_]`)

**Configuration** (`api/config.php`):
- `MAX_FILE_SIZE`: 5MB per file
- `BACKUP_RETENTION_DAYS`: 30
- `MAX_HISTORY_ENTRIES`: 365

CORS headers enabled for cross-origin requests.

## Frontend Patterns

### Navigation & Sections
Four main sections toggled via `showSection(section)`:
1. **training** - Workout selection and timer screen
2. **calendar** - View completed workouts
3. **profile** - User info and settings
4. **data** - Import/export JSON, manage backups

### Workout State Management
Critical state variables (defined globally):
- `currentWorkoutData` - Active workout object with phases, exercises
- `currentPhaseIndex`, `currentExerciseIndex` - Position in routine
- `timerInterval` - Animation loop for countdown display
- `isPaused` - Pause state

**Phases structure** (3-phase routine):
1. WARMUP (2 min)
2. WORK (25 min) - alternating exercises every 30-40 sec
3. COOLDOWN (3 min)

### Key Functions
- `startTodaysWorkout()` - Initialize routine, load progress
- `startTimer()` - Begin countdown, trigger phase transitions
- `togglePause()` - Pause/resume with state persistence
- `goBack()` / `goNext()` - Navigate exercises
- `continueWorkout()` - Resume from `current_progress.json`

## Critical Workflows

### Saving During Workout
1. Every ~5 seconds: `saveProgress()` is called with current state
2. Backup created automatically before overwrite
3. State synced to both server files and localStorage

### Completing a Workout
1. Final phase complete → `saveToHistory()` called
2. Writes to `data/users/[id]/history/[YYYY-MM-DD].json`
3. Clears `current_progress.json` for fresh start

### User ID Changes
1. User inputs new ID in UI
2. `changeUserId(newId)` migrates data (backup old files)
3. Switches localStorage key to new ID
4. Future operations use new directory

## Testing & Debugging
- **Console logging**: Enabled in SW and data_handler.php
- **Network tab**: Monitor API calls to `api/data_handler.php`
- **Application tab**: Check Service Worker registration and Cache storage
- **File system**: Navigate `C:\MAMP\htdocs\training-app\data\users\` to verify file creation

## Common Extension Points
- **New workout routine**: Add to workout data JSON structure in `startTodaysWorkout()`
- **New user stat**: Add field to progress object, ensure `saveProgress()` includes it
- **New section**: Add button in nav, create `section-*` HTML id, implement `showSection()` case
- **Offline enhancements**: Modify `sw.js` sync/push handlers

## Code Style
- **Spanish comments** throughout for context
- **Tailwind CSS** for styling (configured in index.html)
- **Fetch API** for HTTP calls (CORS-enabled)
- **PHP procedural style** for backend simplicity

## Security Notes
- User IDs sanitized server-side (regex filter)
- No authentication/authorization (local-only usage)
- File operations use user-specific directories (isolation)
- 5MB file size limits prevent DoS
