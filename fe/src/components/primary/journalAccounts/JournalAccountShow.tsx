import { useState, useEffect } from "react";
import { journalAccountService } from "../../../api/services/journalAccountService";
import type { JournalAccount } from "../../../types/journalAccount";
import { Card, CardContent, CardHeader, CardTitle } from "../../ui/card";

interface JournalAccountShowProps {
  journalId: string | number;
  onClose?: () => void;
  onEdit?: (journal: JournalAccount) => void;
}

export function JournalAccountShow({ journalId, onClose, onEdit }: JournalAccountShowProps) {
  const [journal, setJournal] = useState<JournalAccount | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchJournal = async () => {
      try {
        setLoading(true);
        setError(null);
        const response = await journalAccountService.getJournalAccount(journalId);
        if (response.success && response.data.length > 0) {
          setJournal(response.data[0]);
        } else {
          setError(response.error || "Journal entry not found");
        }
      } catch (err) {
        setError("Failed to fetch journal entry");
        console.error("Error fetching journal:", err);
      } finally {
        setLoading(false);
      }
    };

    if (journalId) {
      fetchJournal();
    }
  }, [journalId]);

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      maximumFractionDigits: 2
    }).format(amount);
  };

  if (loading) {
    return (
      <Card>
        <CardContent className="p-6">
          <div className="text-center">Loading...</div>
        </CardContent>
      </Card>
    );
  }

  if (error || !journal) {
    return (
      <Card>
        <CardContent className="p-6">
          <div className="text-center text-red-600">
            {error || "Journal entry not found"}
          </div>
          {onClose && (
            <div className="mt-4 text-center">
              <button
                onClick={onClose}
                className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
              >
                Close
              </button>
            </div>
          )}
        </CardContent>
      </Card>
    );
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Journal Entry: {journal.number}</CardTitle>
      </CardHeader>
      <CardContent className="p-6">
        {/* General Information Section */}
        <div className="mb-6">
          <h3 className="text-lg font-bold mb-3">General Information</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Number</div>
              <div className="text-gray-900">{journal.number}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Date</div>
              <div className="text-gray-900">{formatDate(journal.sent_time)}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Source</div>
              <div className="text-gray-900">
                {journal.input_type || 'N/A'} : {journal.input?.number || 'N/A'}
              </div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Created By</div>
              <div className="text-gray-900">{journal.sender?.name || 'N/A'}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Updated By</div>
              <div className="text-gray-900">{journal.handler?.name || 'N/A'}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Total Amount</div>
              <div className="text-gray-900">Rp{formatCurrency(journal.total)}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Status</div>
              <div className="text-gray-900">{journal.status}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Description</div>
              <div className="text-gray-900">{journal.sender_notes || 'N/A'}</div>
            </div>
            <div className="bg-gray-50 p-3 rounded-md">
              <div className="text-sm text-gray-600 font-medium">Notes</div>
              <div className="text-gray-900">{journal.notes || 'N/A'}</div>
            </div>
          </div>
        </div>

        <div className="border-t border-gray-300 my-6"></div>

        {/* Journal Entry Details Section */}
        <div className="mb-6">
          <h3 className="text-lg font-bold mb-3">Journal Entry Details</h3>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Account
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Debit
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Credit
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Notes
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {journal.details.map((detail) => (
                  <tr key={detail.id}>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {detail.detail?.code || '?'} : {detail.detail?.name || 'N/A'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Rp{formatCurrency(detail.debit)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      Rp{formatCurrency(detail.credit)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {detail.notes || 'N/A'}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex gap-3 justify-end mt-8">
          {onClose && (
            <button
              onClick={onClose}
              className="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600"
            >
              Close
            </button>
          )}
          {onEdit && (
            <button
              onClick={() => onEdit(journal)}
              className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Edit Journal
            </button>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
