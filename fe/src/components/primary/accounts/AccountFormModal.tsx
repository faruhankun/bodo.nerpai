import { Modal, Form, Input, Select, Spin } from "antd";
import type { FormInstance } from "antd";
import type { Account, AccountType } from "../../../types/account";



interface AccountFormModalProps {
  isOpen: boolean;
  onOk: () => void;
  onCancel: () => void;
  form: unknown;
  selectedAccount: Account | null;
  accountTypes: AccountType[];
  parentAccounts: Account[];
  searchLoading: boolean;
  onSearchParent: (value: string) => void;
}

export function AccountFormModal({
  isOpen,
  onOk,
  onCancel,
  form,
  selectedAccount,
  accountTypes,
  parentAccounts,
  searchLoading,
  onSearchParent,
}: AccountFormModalProps) {
  return (
    <Modal
      title={selectedAccount ? "Edit Akun" : "Tambah Akun"}
      open={isOpen}
      onOk={onOk}
      onCancel={onCancel}
      okText="Simpan"
      cancelText="Batal"
    >
      <Form form={form as FormInstance} layout="vertical">
        <Form.Item name="name" label="Nama Akun" rules={[{ required: true }]}>
          <Input />
        </Form.Item>
        <Form.Item name="code" label="Kode Akun" rules={[{ required: true }]}>
          <Input />
        </Form.Item>
        <Form.Item
          name="type_id"
          label="Tipe Akun"
          rules={[{ required: true }]}
        >
          <Select 
            placeholder="Pilih tipe akun"
            options={accountTypes.map((type) => ({
              value: type.id,
              label: `${type.id}: ${type.name}`,
            }))}
          />
        </Form.Item>
        <Form.Item name="parent_id" label="Akun Induk" required={false}>
          <Select
            showSearch
            placeholder="Cari akun induk"
            allowClear
            onSearch={onSearchParent}
            filterOption={false}
            loading={searchLoading}
            notFoundContent={
              searchLoading ? <Spin size="small" /> : "Tidak ditemukan"
            }
            options={parentAccounts.map((account) => ({
              value: account.id,
              label: `${account.code}: ${account.name}`,
            }))}
          />
        </Form.Item>
        <Form.Item name="notes" label="Catatan">
          <Input.TextArea rows={2} />
        </Form.Item>
      </Form>
    </Modal>
  );
}
