import { useState, useEffect } from "react";
import { journalAccountService } from "../../../api/services/journalAccountService";
import type { JournalAccount } from "../../../types/journalAccount";
import { Card, CardContent, CardHeader, CardTitle } from "../../ui/card";

interface JournalAccountFormProps {
  journal?: JournalAccount | null;
  onSave?: (journal: JournalAccount) => void;
  onCancel?: () => void;
}

interface FormData {
  sent_time: string;
  sender_notes: string;
  details: Array<{
    account_id: string;
    debit: number;
    credit: number;
    notes: string;
  }>;
}

export function JournalAccountForm({ journal, onSave, onCancel }: JournalAccountFormProps) {
  const [formData, setFormData] = useState<FormData>({
    sent_time: new Date().toISOString().split('T')[0],
    sender_notes: '',
    details: [{ account_id: '', debit: 0, credit: 0, notes: '' }]
  });
  const [loading, setLoading] = useState(false);
  const [accounts, setAccounts] = useState<Array<{ id: string; code: string; name: string }>>([]);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const isEditing = !!journal;

  useEffect(() => {
    if (journal) {
      setFormData({
        sent_time: new Date(journal.sent_time).toISOString().split('T')[0],
        sender_notes: journal.sender_notes || '',
        details: journal.details.map(detail => ({
          account_id: detail.detail_id.toString(),
          debit: detail.debit,
          credit: detail.credit,
          notes: detail.notes || ''
        }))
      });
    }
    // Fetch accounts for dropdown
    fetchAccounts();
  }, [journal]);

  const fetchAccounts = async () => {
    try {
      // This would need to be implemented in the backend
      // For now, using mock data
      setAccounts([
        { id: '1', code: '1001', name: 'Cash' },
        { id: '2', code: '1002', name: 'Bank' },
        { id: '3', code: '2001', name: 'Accounts Payable' },
        { id: '4', code: '3001', name: 'Owner Equity' },
        { id: '5', code: '4001', name: 'Revenue' },
        { id: '6', code: '5001', name: 'Expenses' },
      ]);
    } catch (error) {
      console.error('Error fetching accounts:', error);
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.sent_time) {
      newErrors.sent_time = 'Date is required';
    }

    if (!formData.sender_notes.trim()) {
      newErrors.sender_notes = 'Description is required';
    }

    let totalDebit = 0;
    let totalCredit = 0;

    formData.details.forEach((detail, index) => {
      if (!detail.account_id) {
        newErrors[`details.${index}.account_id`] = 'Account is required';
      }
      if (detail.debit < 0) {
        newErrors[`details.${index}.debit`] = 'Debit cannot be negative';
      }
      if (detail.credit < 0) {
        newErrors[`details.${index}.credit`] = 'Credit cannot be negative';
      }
      if (detail.debit === 0 && detail.credit === 0) {
        newErrors[`details.${index}.amount`] = 'Either debit or credit must be greater than 0';
      }
      if (detail.debit > 0 && detail.credit > 0) {
        newErrors[`details.${index}.amount`] = 'Cannot have both debit and credit';
      }

      totalDebit += detail.debit;
      totalCredit += detail.credit;
    });

    if (Math.abs(totalDebit - totalCredit) > 0.01) {
      newErrors.balance = 'Total debits must equal total credits';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setLoading(true);
    try {
      const data = {
        sent_time: formData.sent_time,
        sender_notes: formData.sender_notes,
        total: formData.details.reduce((sum, detail) => sum + detail.debit + detail.credit, 0),
        details: formData.details
      };

      let response;
      if (isEditing && journal) {
        response = await journalAccountService.updateJournalAccount(journal.id, data);
      } else {
        response = await journalAccountService.createJournalAccount(data);
      }

      if (response.success && onSave) {
        onSave(response.data[0]);
      }
    } catch (error) {
      console.error('Error saving journal:', error);
      setErrors({ submit: 'Failed to save journal entry' });
    } finally {
      setLoading(false);
    }
  };

  const addDetail = () => {
    setFormData(prev => ({
      ...prev,
      details: [...prev.details, { account_id: '', debit: 0, credit: 0, notes: '' }]
    }));
  };

  const removeDetail = (index: number) => {
    if (formData.details.length > 1) {
      setFormData(prev => ({
        ...prev,
        details: prev.details.filter((_, i) => i !== index)
      }));
    }
  };

  const updateDetail = (index: number, field: string, value: any) => {
    setFormData(prev => ({
      ...prev,
      details: prev.details.map((detail, i) => 
        i === index ? { ...detail, [field]: value } : detail
      )
    }));
  };

  const getFieldError = (field: string): string => {
    return errors[field] || '';
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle>
          {isEditing ? `Edit Journal Entry: ${journal?.number}` : 'Create New Journal Entry'}
        </CardTitle>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Basic Information */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Date *
              </label>
              <input
                type="date"
                value={formData.sent_time}
                onChange={(e) => setFormData(prev => ({ ...prev, sent_time: e.target.value }))}
                className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                  getFieldError('sent_time') ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {getFieldError('sent_time') && (
                <p className="text-red-500 text-sm mt-1">{getFieldError('sent_time')}</p>
              )}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Description *
              </label>
              <input
                type="text"
                value={formData.sender_notes}
                onChange={(e) => setFormData(prev => ({ ...prev, sender_notes: e.target.value }))}
                className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                  getFieldError('sender_notes') ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder="Enter description"
              />
              {getFieldError('sender_notes') && (
                <p className="text-red-500 text-sm mt-1">{getFieldError('sender_notes')}</p>
              )}
            </div>
          </div>

          {/* Journal Details */}
          <div>
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-medium">Journal Details</h3>
              <button
                type="button"
                onClick={addDetail}
                className="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
              >
                Add Line
              </button>
            </div>

            {formData.details.map((detail, index) => (
              <div key={index} className="grid grid-cols-12 gap-4 mb-4 p-4 border rounded-md">
                <div className="col-span-3">
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Account *
                  </label>
                  <select
                    value={detail.account_id}
                    onChange={(e) => updateDetail(index, 'account_id', e.target.value)}
                    className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                      getFieldError(`details.${index}.account_id`) ? 'border-red-500' : 'border-gray-300'
                    }`}
                  >
                    <option value="">Select Account</option>
                    {accounts.map(account => (
                      <option key={account.id} value={account.id}>
                        {account.code} - {account.name}
                      </option>
                    ))}
                  </select>
                  {getFieldError(`details.${index}.account_id`) && (
                    <p className="text-red-500 text-sm mt-1">{getFieldError(`details.${index}.account_id`)}</p>
                  )}
                </div>

                <div className="col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Debit
                  </label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={detail.debit}
                    onChange={(e) => updateDetail(index, 'debit', parseFloat(e.target.value) || 0)}
                    className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                      getFieldError(`details.${index}.debit`) ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="0.00"
                  />
                  {getFieldError(`details.${index}.debit`) && (
                    <p className="text-red-500 text-sm mt-1">{getFieldError(`details.${index}.debit`)}</p>
                  )}
                </div>

                <div className="col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Credit
                  </label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={detail.credit}
                    onChange={(e) => updateDetail(index, 'credit', parseFloat(e.target.value) || 0)}
                    className={`w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                      getFieldError(`details.${index}.credit`) ? 'border-red-500' : 'border-gray-300'
                    }`}
                    placeholder="0.00"
                  />
                  {getFieldError(`details.${index}.credit`) && (
                    <p className="text-red-500 text-sm mt-1">{getFieldError(`details.${index}.credit`)}</p>
                  )}
                </div>

                <div className="col-span-3">
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Notes
                  </label>
                  <input
                    type="text"
                    value={detail.notes}
                    onChange={(e) => updateDetail(index, 'notes', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional notes"
                  />
                </div>

                <div className="col-span-1">
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    &nbsp;
                  </label>
                  <button
                    type="button"
                    onClick={() => removeDetail(index)}
                    disabled={formData.details.length === 1}
                    className="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    ×
                  </button>
                </div>

                {(getFieldError(`details.${index}.amount`)) && (
                  <div className="col-span-12">
                    <p className="text-red-500 text-sm">{getFieldError(`details.${index}.amount`)}</p>
                  </div>
                )}
              </div>
            ))}

            {getFieldError('balance') && (
              <div className="text-red-500 text-sm mt-2">{getFieldError('balance')}</div>
            )}
          </div>

          {/* Action Buttons */}
          <div className="flex justify-end gap-3 pt-6 border-t">
            {onCancel && (
              <button
                type="button"
                onClick={onCancel}
                className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
              >
                Cancel
              </button>
            )}
            <button
              type="submit"
              disabled={loading}
              className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? 'Saving...' : (isEditing ? 'Update' : 'Create')}
            </button>
          </div>

          {getFieldError('submit') && (
            <div className="text-red-500 text-sm text-center">{getFieldError('submit')}</div>
          )}
        </form>
      </CardContent>
    </Card>
  );
}
