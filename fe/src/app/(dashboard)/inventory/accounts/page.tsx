import { useEffect, useState, useCallback } from "react";
import { Form, Button, Flex, Pagination, Modal, Input } from "antd";
import { toast } from "sonner";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "../../../../components/ui/card";
import { AccountsTable } from "../../../../components/primary/accounts/AccountsTable";
import { AccountFormModal } from "../../../../components/primary/accounts/AccountFormModal";
import { accountService } from "../../../../api/services/accounts.service";
import type { Account, AccountType, ApiDataTable } from "../../../../types/account";

import { getApi } from "../../../../api/index";

import { AccountTransactionModal } from "../../../../components/primary/accounts/AccountTransactionModal";



const AccountsPage = () => {
  const [form] = Form.useForm();
  const [selectedAccount, setSelectedAccount] = useState<Account | null>(null);
  const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
  const [accountTypes, setAccountTypes] = useState<AccountType[]>([]);
  const [parentAccounts, setParentAccounts] = useState<Account[]>([]);
  const [searchLoading, setSearchLoading] = useState<boolean>(false);
  const [selectedRowKeys, setSelectedRowKeys] = useState<React.Key[]>([]);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState<boolean>(false);

  const [accountsTree, setAccountsTree] = useState<Account[]>([]);
  const [expandedRowKeys, setExpandedRowKeys] = useState<React.Key[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [search, setSearch] = useState<string>("");


  // pagination
  const [page, setPage] = useState<number>(1);
  const [pageSize, setPageSize] = useState<number>(10);
  const [total, setTotal] = useState<number>(0);


  const [modalTransactionVisible, setModalTransactionVisible] = useState<boolean>(false);


  const api = getApi();


  const fetchRootAccounts = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("accounts/data", {
        searchParams: {
          parent_only: "true",
          q: search,
          start: (page - 1) * pageSize,
          length: pageSize,
          limit: "all",
        },
      });
      const json: ApiDataTable = await res.json();
      console.log("data", json.data);

      const sanitized = json.data.map((acc: Account) => {
        return {
          ...acc,
          children: acc.has_children ? acc.children : null,
        };
      });

      setAccountsTree(sanitized);
      setTotal(json.recordsFiltered);
      
      setExpandedRowKeys([]);
    } catch (err) {
      console.error("Gagal fetch akun root", err);
      toast.error("Gagal memuat akun utama");
    } finally {
      setLoading(false);
    }
  }, [search, page, pageSize]);



  function insertChildren(tree: Account[], parentId: number | string, children: Account[]): Account[] {
    return tree.map(node => {
      if (node.id === parentId) {
        return {
          ...node,
          children,
        };
      } else if (node.children) {
        return {
          ...node,
          children: insertChildren(node.children, parentId, children),
        };
      }
      return node;
    });
  }


  const handleExpand = async (expanded: boolean, record: Account) => {
    if (expanded) {
      try {
        const res = await api.get("accounts/data", {
          searchParams: {
            parent_id: record.id,
            q: '',
            limit: 'all',
          },
        });
        const json: ApiDataTable = await res.json();

        const sanitized = json.data.map((acc: Account) => {
          return {
            ...acc,
            children: acc.has_children ? acc.children : null,
          };
        });

        setAccountsTree(prev => insertChildren(prev, record.id, sanitized));
      } catch (err) {
        console.error("Gagal memuat anak akun", err);
        toast.error("Gagal memuat anak akun");
      }
    }

    setExpandedRowKeys(prev =>
      expanded
        ? [...prev, record.id]
        : prev.filter(key => key !== record.id)
    );

    console.log("expanded", expanded, record);
  };



  const handlePaginationChange = (newPage: number, newPageSize: number) => {
    setPage(newPage);
    setPageSize(newPageSize);
  };




  const fetchDropdowns = useCallback(async () => {
    try {
      const { data } = await accountService.getAccountTypes();
      setAccountTypes(data || []);
    } catch (err) {
      console.error("Gagal ambil data dropdown:", err);
      toast.error("Gagal memuat tipe akun.");
    }
  }, []);

  useEffect(() => {
    fetchDropdowns();
    fetchRootAccounts();
  }, [fetchDropdowns, fetchRootAccounts]);



  const handleSearchParent = useCallback(async (value: string) => {
    if (!value) return;
    setSearchLoading(true);
    try {
      const { data } = await accountService.searchParentAccounts(value);
      setParentAccounts(data || []);
    } catch (err) {
      console.error("Gagal mencari akun induk:", err);
    } finally {
      setSearchLoading(false);
    }
  }, []);

  const handleAddNewAccount = () => {
    form.resetFields();
    setSelectedAccount(null);
    setIsModalOpen(true);
    setParentAccounts([]);
  };

  const handleRowClick = async (record: Account) => {
    setSelectedAccount(record);
    form.setFieldsValue(record);
    setIsModalOpen(true);
    if (record.parent_id) {
      try {
        const { data } = await accountService.getAccountById(record.parent_id);
        setParentAccounts(data || []);
      } catch {
        toast.error("Gagal memuat akun induk.");
      }
    }
  };

  const handleModalCancel = () => {
    setIsModalOpen(false);
    setSelectedAccount(null);
  };

  const handleModalOk = async () => {
    try {
      const values = await form.validateFields();

      if (values.parent_id === "") {
        values.parent_id = null;
      }

      await toast.promise(
        accountService.saveAccount(values, selectedAccount?.id || null),
        {
          loading: "Sedang menyimpan akun...",
          success: (result) => {
            fetchRootAccounts();
            setIsModalOpen(false);
            setSelectedAccount(null);
            return result.toast || "Berhasil menyimpan akun";
          },
          error: (err: Error) => `Gagal menyimpan akun: ${err.message}`,
        },
      );
    } catch (errorInfo) {
      // Validation failed
      console.log("Failed:", errorInfo);
    }
  };

  const handleDeleteMultiple = async () => {
    // const selectedAccounts = accountsTree.flatMap(acc => 

    // const hasChild = selectedAccounts.some(acc => acc.has_children);
    // if (hasChild) {
    //   toast.error("Tidak dapat menghapus akun yang memiliki anak.");
    //   return;
    // }

    await toast.promise(accountService.deleteAccounts(selectedRowKeys), {
      loading: "Sedang menghapus akun terpilih...",
      success: () => {
        setSelectedRowKeys([]);
        setIsDeleteModalOpen(false);
        fetchRootAccounts();
        return "Akun terpilih berhasil dihapus";
      },
      error: "Gagal menghapus akun.",
    });
  };



  return (
    <Card>
      <CardHeader>
        <CardTitle>Daftar Akun</CardTitle>
        <CardDescription>Berikut adalah daftar akun akun anda</CardDescription>
      </CardHeader>

      <Flex
        gap="small"
        wrap
        justify="space-between"
        style={{ margin: "8px 16px" }}
      >
        <Button type="primary" onClick={handleAddNewAccount}>
          + Tambah Akun
        </Button>

        <Input.Search
          placeholder="Cari akun..."
          allowClear  
          enterButton
          value={search}
          onChange={(e: React.ChangeEvent<HTMLInputElement>) => setSearch(e.target.value)}
          onSearch={(value: string) => {
            setSearch(value);
            fetchRootAccounts();
          }}
          style={{ width: "300px" }}
        />
      </Flex>

      <Flex
        style={{ margin: "8px 16px" }}
      >
        {selectedRowKeys.length > 0 ? (
          <Button danger onClick={() => setIsDeleteModalOpen(true)}>
            Hapus Terpilih ({selectedRowKeys.length})
          </Button>
        ) : (
          <div />
        )}
      </Flex>

      <CardContent className="space-y-4">
        <AccountsTable
          loading={loading}
          data={accountsTree}
          selectedRowKeys={selectedRowKeys}
          onRowClick={handleRowClick}
          onSelectionChange={setSelectedRowKeys}
          expandedRowKeys={expandedRowKeys}
          onExpand={handleExpand}
          setModalTransactionVisible={setModalTransactionVisible}
          onSaldoClick={(acc) => {
            setSelectedAccount(acc);
            setModalTransactionVisible(true);
          }}
        />
        <div className="flex justify-end">
          <Pagination
            current={page}
            pageSize={pageSize}
            total={total}
            onChange={handlePaginationChange}
            showSizeChanger
            showTotal={(total: number, range: [number, number]) =>
              `${range[0]}-${range[1]} of ${total} items`
            }
          />
        </div>
      </CardContent>

      <AccountFormModal
        isOpen={isModalOpen}
        onOk={handleModalOk}
        onCancel={handleModalCancel}
        form={form}
        selectedAccount={selectedAccount}
        accountTypes={accountTypes}
        parentAccounts={parentAccounts}
        searchLoading={searchLoading}
        onSearchParent={handleSearchParent}
      />

      <Modal
        title="Konfirmasi Hapus"
        open={isDeleteModalOpen}
        onCancel={() => setIsDeleteModalOpen(false)}
        onOk={handleDeleteMultiple}
        okText="Ya, Hapus"
        cancelText="Batal"
      >
        <p>
          Apakah kamu yakin ingin menghapus {selectedRowKeys.length} akun ini?
          Tindakan ini tidak bisa dibatalkan.
        </p>
      </Modal>


      <AccountTransactionModal
        visible={modalTransactionVisible}
        onClose={() => setModalTransactionVisible(false)}
        accountId={selectedAccount?.id || 0}
        startDate={new Date().getFullYear() + '-01-01'}
        endDate={new Date().toISOString().split('T')[0]}
        accountData={selectedAccount}
      />
    </Card>
  );
};

export default AccountsPage;
