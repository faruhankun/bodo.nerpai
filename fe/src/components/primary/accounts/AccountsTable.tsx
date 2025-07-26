import { Table } from "antd";
import type { TableColumnsType } from "antd";
import type { Account } from "../../../types/account";



interface AccountsTableProps {
  loading: boolean;
  data: Account[];
  selectedRowKeys: React.Key[];
  onRowClick: (record: Account) => void;
  onSelectionChange: (selectedKeys: React.Key[]) => void;
  expandedRowKeys: React.Key[];
  onExpand: (expanded: boolean, record: Account) => void;
  setModalTransactionVisible: (visible: boolean) => void
  onSaldoClick: (account: Account) => void
}


export function AccountsTable({
  loading,
  data,
  selectedRowKeys,
  onRowClick,
  onSelectionChange,
  expandedRowKeys,
  onExpand: handleExpand,
  onSaldoClick,
}: AccountsTableProps) {

  const columns: TableColumnsType<Account> = [
    { title: "ID", dataIndex: "id", key: "id" },
    { title: "Kode", dataIndex: "code", key: "code" },
    { title: "Nama Akun", dataIndex: "name", key: "name" },
    {
      title: "Tipe",
      dataIndex: ["type", "name"],
      key: "type.name",
    },
    // { title: "Notes", dataIndex: "notes", key: "notes" },
    { title: "Saldo", key: "balance", render: (_, record) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick(record);
        }}
        style={{ cursor: "pointer" }}
      >
        {record.balance || 0 }
      </a>
    )},
  ];


  return (
    <div className="overflow-x-auto">
      <Table
        columns={columns}
        dataSource={data}
        rowKey="id"
        loading={loading}
        pagination={false}
        expandable={{
          expandedRowKeys,
          onExpand: handleExpand,
          rowExpandable: (record: Account) => {
            console.log("checking expandable for", record.id, record.has_children);
            return !!record.has_children;
          }
        }}
        onRow={(record: Account) => ({
          onClick: () => onRowClick(record),
          style: { cursor: "pointer" },
        })}
        rowSelection={{
          selectedRowKeys,
          onChange: onSelectionChange,
          getCheckboxProps: () => ({
            disabled: false,  
          }),
        }}
      />
    </div>
  );
}
