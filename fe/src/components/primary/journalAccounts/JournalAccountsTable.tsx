import { useState, useEffect } from "react";
import { journalAccountService } from "../../../api/services/journalAccountService";
import type { JournalAccount } from "../../../types/journalAccount";
import { Card, CardContent, CardHeader, CardTitle } from "../../ui/card";

interface JournalAccountsTableProps {
  onEdit?: (journal: JournalAccount) => void;
  onShow?: (journal: JournalAccount) => void;
  onDelete?: (journal: JournalAccount) => void;
}

export function JournalAccountsTable({ onEdit, onShow, onDelete }: JournalAccountsTableProps) {
  const [data, setData] = useState<JournalAccount[]>([]);
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({
    current: 1,
    pageSize: 10,
    total: 0,
  });
  const [searchValue, setSearchValue] = useState("");

  const fetchData = async (params?: any) => {
    try {
      setLoading(true);
      const response = await journalAccountService.getJournalAccountsData(params);
      if (response.success) {
        setData(response.data);
        setPagination(prev => ({
          ...prev,
          total: response.recordsFiltered,
        }));
      }
    } catch (error) {
      console.error("Error fetching journal accounts:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData({ pagination });
  }, [pagination.current, pagination.pageSize]);

  const handleSearch = (value: string) => {
    setSearchValue(value);
    setPagination(prev => ({ ...prev, current: 1 }));
    fetchData({ 
      pagination: { ...pagination, current: 1 },
      search: { value }
    });
  };

  const handlePageChange = (page: number, pageSize: number) => {
    setPagination(prev => ({ ...prev, current: page, pageSize }));
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      maximumFractionDigits: 2
    }).format(amount);
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle>Journal Accounts</CardTitle>
        <div className="flex gap-2">
          <input
            type="text"
            placeholder="Search..."
            value={searchValue}
            onChange={(e) => handleSearch(e.target.value)}
            className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </CardHeader>
      <CardContent>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ID
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Number
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Description
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan={6} className="px-6 py-4 text-center">
                    Loading...
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-6 py-4 text-center text-gray-500">
                    No journal accounts found
                  </td>
                </tr>
              ) : (
                data.map((journal) => (
                  <tr key={journal.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {journal.id}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {formatDate(journal.sent_time)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {journal.number}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {journal.sender_notes || '-'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                      Rp{formatCurrency(journal.total)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex gap-2">
                        {onShow && (
                          <button
                            onClick={() => onShow(journal)}
                            className="text-blue-600 hover:text-blue-900"
                          >
                            View
                          </button>
                        )}
                        {onEdit && (
                          <button
                            onClick={() => onEdit(journal)}
                            className="text-indigo-600 hover:text-indigo-900"
                          >
                            Edit
                          </button>
                        )}
                        {onDelete && (
                          <button
                            onClick={() => onDelete(journal)}
                            className="text-red-600 hover:text-red-900"
                          >
                            Delete
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        
        {/* Pagination */}
        <div className="flex items-center justify-between mt-4">
          <div className="text-sm text-gray-700">
            Showing {((pagination.current - 1) * pagination.pageSize) + 1} to{' '}
            {Math.min(pagination.current * pagination.pageSize, pagination.total)} of{' '}
            {pagination.total} results
          </div>
          <div className="flex gap-2">
            <button
              onClick={() => handlePageChange(pagination.current - 1, pagination.pageSize)}
              disabled={pagination.current === 1}
              className="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Previous
            </button>
            <button
              onClick={() => handlePageChange(pagination.current + 1, pagination.pageSize)}
              disabled={pagination.current * pagination.pageSize >= pagination.total}
              className="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Next
            </button>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
