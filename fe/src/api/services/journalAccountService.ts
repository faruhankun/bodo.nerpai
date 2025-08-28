import { api } from "../index";
import type { JournalAccountDataTable, JournalAccountResponse } from "../../types/journalAccount";

interface JournalAccountFormData {
  sent_time: string;
  sender_notes: string;
  total: number;
  details: Array<{
    account_id: string;
    debit: number;
    credit: number;
    notes: string;
  }>;
}

export const journalAccountService = {
  // Get journal accounts data for DataTable
  async getJournalAccountsData(params?: any): Promise<JournalAccountDataTable> {
    const response = await api.get("journal_accounts/data", { searchParams: params });
    return response.json();
  },

  // Get single journal account
  async getJournalAccount(id: string | number): Promise<JournalAccountResponse> {
    const response = await api.get(`journal_accounts/${id}`);
    return response.json();
  },

  // Create new journal account
  async createJournalAccount(data: JournalAccountFormData): Promise<JournalAccountResponse> {
    const response = await api.post("journal_accounts", { json: data });
    return response.json();
  },

  // Update journal account
  async updateJournalAccount(id: string | number, data: JournalAccountFormData): Promise<JournalAccountResponse> {
    const response = await api.put(`journal_accounts/${id}`, { json: data });
    return response.json();
  },

  // Delete journal account
  async deleteJournalAccount(id: string | number): Promise<JournalAccountResponse> {
    const response = await api.delete(`journal_accounts/${id}`);
    return response.json();
  },

  // Export journal accounts
  async exportJournalAccounts(params?: any): Promise<Blob> {
    const response = await api.get("journal_accounts/export", { searchParams: params });
    return response.blob();
  },

  // Import journal accounts
  async importJournalAccounts(formData: FormData): Promise<JournalAccountResponse> {
    const response = await api.post("journal_accounts/import", { body: formData });
    return response.json();
  },

  // Get import template
  async getImportTemplate(): Promise<Blob> {
    const response = await api.get("journal_accounts/import-template");
    return response.blob();
  },
};
