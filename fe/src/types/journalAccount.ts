interface JournalAccountDetail {
  id: number | string;
  transaction_id: number | string;
  detail_type: string;
  detail_id: number | string;
  debit: number;
  credit: number;
  notes?: string;
  detail?: {
    id: number | string;
    code: string;
    name: string;
  };
}

interface JournalAccount {
  id: number | string;
  space_id: number | string;
  model_type: string;
  number: string;
  sender_type: string;
  sender_id: number | string;
  input_type?: string;
  input_id?: number | string;
  sent_time: string;
  sender_notes?: string;
  total: number;
  status: string;
  notes?: string;
  sender?: {
    id: number | string;
    name: string;
  };
  handler?: {
    id: number | string;
    name: string;
  };
  input?: {
    id: number | string;
    number: string;
  };
  details: JournalAccountDetail[];
}

interface JournalAccountDataTable {
  data: JournalAccount[];
  recordsFiltered: number;
  success: boolean;
}

interface JournalAccountResponse {
  data: JournalAccount[];
  success: boolean;
  message?: string;
  error?: string;
}

export type {
  JournalAccount,
  JournalAccountDetail,
  JournalAccountDataTable,
  JournalAccountResponse,
};
