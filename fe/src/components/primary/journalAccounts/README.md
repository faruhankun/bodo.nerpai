# Journal Accounts React Components

This directory contains React components for managing journal accounts, converted from the original Laravel Blade views.

## Components

### JournalAccountsApp
The main application component that manages the overall state and view modes.

**Props:** None

**Features:**
- Manages view modes (list, show, create, edit)
- Handles navigation between different views
- Provides a unified interface for journal account management

### JournalAccountsTable
Displays a table of journal accounts with pagination, search, and actions.

**Props:**
- `onEdit?: (journal: JournalAccount) => void` - Callback when edit button is clicked
- `onShow?: (journal: JournalAccount) => void` - Callback when view button is clicked
- `onDelete?: (journal: JournalAccount) => void` - Callback when delete button is clicked

**Features:**
- Responsive table with sorting
- Search functionality
- Pagination
- Action buttons for each row

### JournalAccountShow
Displays detailed information about a specific journal entry.

**Props:**
- `journalId: string | number` - ID of the journal to display
- `onClose?: () => void` - Callback when close button is clicked
- `onEdit?: (journal: JournalAccount) => void` - Callback when edit button is clicked

**Features:**
- General information display
- Journal entry details table
- Action buttons (close, edit)

### JournalAccountForm
Form component for creating and editing journal entries.

**Props:**
- `journal?: JournalAccount | null` - Journal to edit (null for create mode)
- `onSave?: (journal: JournalAccount) => void` - Callback when form is submitted
- `onCancel?: () => void` - Callback when cancel button is clicked

**Features:**
- Form validation
- Dynamic journal detail lines
- Account selection dropdown
- Debit/credit input fields
- Notes field for each line

## Usage

```tsx
import { JournalAccountsApp } from './components/primary/journalAccounts';

function App() {
  return (
    <div className="min-h-screen bg-gray-50">
      <JournalAccountsApp />
    </div>
  );
}
```

## API Integration

The components use the `journalAccountService` for API calls. Make sure the backend endpoints are properly configured:

- `GET /api/journal_accounts/data` - Get paginated journal accounts
- `GET /api/journal_accounts/{id}` - Get specific journal account
- `POST /api/journal_accounts` - Create new journal account
- `PUT /api/journal_accounts/{id}` - Update journal account
- `DELETE /api/journal_accounts/{id}` - Delete journal account

## Styling

Components use Tailwind CSS for styling and include:
- Responsive design
- Dark mode support (via CSS variables)
- Consistent spacing and typography
- Hover states and transitions

## Dependencies

- React 19+
- TypeScript
- Tailwind CSS
- Custom UI components (Card, Modal)
- Utility functions (cn for class merging)
