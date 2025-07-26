// src/api/services/account.service.ts
import { getApi } from "../../api/index";
import type {
  Account,
  AccountType,
  ApiDataTable,
  ApiResponse,
} from "../../types/account";



/**
 * Fetches a paginated list of accounts.
 */
async function getAccounts(
  currentPage: number,
  currentPageSize: number,
): Promise<ApiDataTable> {
  const api = getApi();

  const response = await api.get("accounts/data", {
    searchParams: {
      draw: 1,
      start: (currentPage - 1) * currentPageSize,
      length: currentPageSize,
    },
  });
  return response.json<ApiDataTable>();
}

/**
 * Fetches all available account types for dropdowns.
 */
async function getAccountTypes(): Promise<{ data: AccountType[] }> {
  const api = getApi();

  const response = await api.get("accounts/types/data");
  return response.json<{ data: AccountType[] }>();
}

/**
 * Searches for parent accounts based on a query string.
 */
async function searchParentAccounts(query: string): Promise<ApiDataTable> {
  const api = getApi();

  const response = await api.get("accounts/search", {
    searchParams: {
      q: query,
    },
  });
  return response.json<ApiDataTable>();
}

/**
 * Fetches a single account by its ID, typically for populating the parent account field.
 */
async function getAccountById(
  id: number | string,
): Promise<{ data: Account[] }> {
  const api = getApi();

  const response = await api.get(`accounts/${id}`);
  return response.json<{ data: Account[] }>();
}

/**
 * Creates a new account or updates an existing one.
 */
async function saveAccount(
  accountData: Partial<Account>,
  accountId: number | string | null,
): Promise<ApiResponse> {
  const api = getApi();

  const payload = { ...accountData };
  if (!payload.parent_id) {
    payload.parent_id = null;
  }

  const response = accountId
    ? await api.put(`accounts/${accountId}`, { json: payload })
    : await api.post("accounts", { json: payload });

  const result: ApiResponse = await response.json();
  if (!result.success) {
    throw new Error(result.toast || "An unknown error occurred");
  }
  return result;
}

/**
 * Deletes multiple accounts based on their IDs.
 */
async function deleteAccounts(accountIds: React.Key[]): Promise<void> {
  const api = getApi();

  const deletePromises = accountIds.map((id) =>
    api.delete(`accounts/${id}?request_source=api`),
  );
  await Promise.all(deletePromises);
}

export const accountService = {
  getAccounts,
  getAccountTypes,
  searchParentAccounts,
  getAccountById,
  saveAccount,
  deleteAccounts,
};
