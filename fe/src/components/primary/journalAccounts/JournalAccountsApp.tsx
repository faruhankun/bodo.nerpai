import { useState } from "react";
import { JournalAccountsTable } from "./JournalAccountsTable";
import { JournalAccountShow } from "./JournalAccountShow";
import { JournalAccountForm } from "./JournalAccountForm";
import type { JournalAccount } from "../../../types/journalAccount";
import { Card, CardHeader, CardTitle } from "../../ui/card";

type ViewMode = 'list' | 'show' | 'create' | 'edit';

export function JournalAccountsApp() {
  const [viewMode, setViewMode] = useState<ViewMode>('list');
  const [selectedJournal, setSelectedJournal] = useState<JournalAccount | null>(null);
  const [editingJournal, setEditingJournal] = useState<JournalAccount | null>(null);

  const handleShow = (journal: JournalAccount) => {
    setSelectedJournal(journal);
    setViewMode('show');
  };

  const handleEdit = (journal: JournalAccount) => {
    setEditingJournal(journal);
    setViewMode('edit');
  };

  const handleCreate = () => {
    setEditingJournal(null);
    setViewMode('create');
  };

  const handleDelete = async (journal: JournalAccount) => {
    if (confirm(`Are you sure you want to delete journal entry ${journal.number}?`)) {
      try {
        // Delete logic would go here
        console.log('Deleting journal:', journal.id);
        // Refresh the list
        setViewMode('list');
      } catch (error) {
        console.error('Error deleting journal:', error);
      }
    }
  };

  const handleSave = (_journal: JournalAccount) => {
    // Refresh the list and go back to list view
    setViewMode('list');
    setEditingJournal(null);
    setSelectedJournal(null);
  };

  const handleCancel = () => {
    setViewMode('list');
    setEditingJournal(null);
    setSelectedJournal(null);
  };

  const handleClose = () => {
    setViewMode('list');
    setSelectedJournal(null);
  };

  const renderContent = () => {
    switch (viewMode) {
      case 'show':
        return (
          <JournalAccountShow
            journalId={selectedJournal?.id || ''}
            onClose={handleClose}
            onEdit={() => selectedJournal && handleEdit(selectedJournal)}
          />
        );
      
      case 'create':
        return (
          <JournalAccountForm
            onSave={handleSave}
            onCancel={handleCancel}
          />
        );
      
      case 'edit':
        return (
          <JournalAccountForm
            journal={editingJournal}
            onSave={handleSave}
            onCancel={handleCancel}
          />
        );
      
      default:
        return (
          <div className="space-y-6">
            {/* Header with Create Button */}
            <Card>
              <CardHeader>
                <div className="flex justify-between items-center">
                  <CardTitle>Journal Accounts</CardTitle>
                  <button
                    onClick={handleCreate}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                  >
                    Create New Journal Entry
                  </button>
                </div>
              </CardHeader>
            </Card>

            {/* Table */}
            <JournalAccountsTable
              onShow={handleShow}
              onEdit={handleEdit}
              onDelete={handleDelete}
            />
          </div>
        );
    }
  };

  return (
    <div className="container mx-auto px-4 py-8">
      {renderContent()}
    </div>
  );
}
