interface AccountType {
  id: number | string;
  name: string;
}

interface Account {
  id: number | string;
  code: string;
  name: string;
  notes?: string;
  type_id: number | string;
  type: AccountType;
  parent_id?: number | string | null;
  children?: Account[] | null;
  has_children?: boolean;
  children_count?: number;
  balance?: number;
  status?: string;
}

interface ApiDataTable {
  data: Account[];
  recordsFiltered: number;
}

interface ApiResponse {
  data: unknown;
  success: boolean;
  toast: string;
}

interface ApiResponseType {
  data: AccountType[];
}

export type {
  AccountType,
  Account,
  ApiDataTable,
  ApiResponse,
  ApiResponseType,
};
