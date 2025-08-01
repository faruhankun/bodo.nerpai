import { useEffect, useState } from "react";
import { Modal, Table, DatePicker, Pagination } from "antd";
import dayjs, { Dayjs } from "dayjs";

import { api } from "../../../api/index";
import type { Account } from "../../../types/account";

const { RangePicker } = DatePicker;

interface TransactionItem {
  id?: string | number;
  transaction?: {
    id: number;
    number: string;
    sender_notes: string;
    sent_time: string;
  };
  notes?: string;
  debit?: string | number;
  credit?: string | number;
  running_balance?: number;
  key?: string;
}

interface ApiAccountTransaction {
  data: TransactionItem[];
  initial_balance: string | number;
  initial_debit: string | number;
  initial_credit: string | number;
  page_debit: string | number;
  page_credit: string | number;
  total: number | string;
}

interface AccountTransactionModalProps {
  visible: boolean;
  onClose: () => void;
  accountId: number | string;
  startDate: string;
  endDate: string;
  accountData: Account | null;
}

const AccountTransactionModal = ({
  visible,
  onClose,
  accountId,
  startDate,
  endDate,
  accountData,
}: AccountTransactionModalProps) => {
  const [loading, setLoading] = useState<boolean>(false);
  const [data, setData] = useState<TransactionItem[]>([]);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 10, total: 0 });
  // const [search, setSearch] = useState<string>("");
  const [dateRange, setDateRange] = useState<[Dayjs, Dayjs] | null>(null);

  const [initialDebit, setInitialDebit] = useState<number>(0);
  const [initialCredit, setInitialCredit] = useState<number>(0);
  const [initialBalance, setInitialBalance] = useState<number>(0);
  const [finalDebit, setFinalDebit] = useState<number>(0);
  const [finalCredit, setFinalCredit] = useState<number>(0);
  const [finalBalance, setFinalBalance] = useState<number>(0);

  const fetchData = async (params: {
    startDate?: string | null;
    endDate?: string | null;
    pagination?: { current?: number; pageSize?: number };
  }) => {
    setLoading(true);
    try {
      const response = await api.get("accounts/transactions", {
        searchParams: {
          account_id: accountId,
          // search: search,
          start_date: params.startDate ?? dateRange?.[0]?.format("YYYY-MM-DD") ?? startDate,
          end_date: params.endDate ?? dateRange?.[1]?.format("YYYY-MM-DD") ?? endDate,
          page: params.pagination?.current ?? 1,
          per_page: params.pagination?.pageSize ?? pagination.pageSize,
        },
      });

      const res: ApiAccountTransaction = await response.json();

      let runningBalance = parseFloat(res.initial_balance as string) || 0;
      // let runningDebit = parseFloat(res.initial_debit as string) || 0;
      // let runningCredit = parseFloat(res.initial_credit as string) || 0;

      console.log(res);
      
      const calculatedData = res.data.map((item) => {
        const debit = parseFloat(item.debit as string) || 0;
        const credit = parseFloat(item.credit as string) || 0;

        // runningDebit += debit;
        // runningCredit += credit;
        runningBalance += debit - credit;

        return {
          ...item,
          running_balance: runningBalance,
        };
      });

      setInitialBalance(parseFloat(res.initial_balance as string) || 0);
      setInitialDebit(parseFloat(res.initial_debit as string || "0"));
      setInitialCredit(parseFloat(res.initial_credit as string || "0"));

      setFinalDebit(parseFloat(res.page_debit as string) || 0);
      setFinalCredit(parseFloat(res.page_credit as string) || 0);
      setFinalBalance(runningBalance);
      
      setData(calculatedData);
      setPagination((prev) => ({
        current: params.pagination?.current ?? prev.current,
        pageSize: params.pagination?.pageSize ?? prev.pageSize,
        total: Number(res.total),
      }));
    } catch (err) {
      console.error(err);
    }
    setLoading(false);
  };

  useEffect(() => {
    console.log("visible", visible, startDate, endDate);

    if (visible && startDate && endDate) {
      setDateRange([dayjs(startDate), dayjs(endDate)]);
    }
  }, [visible, startDate, endDate]);

  useEffect(() => {
    if (visible) {
      fetchData({ pagination });
    }
  }, [visible]);

  const columns = [
    {
      title: "Tanggal",
      key: "date",
      render: (record: TransactionItem) =>
        record.key === "saldo-awal" ? (
          <span className="text-lg font-bold">Saldo Awal</span>
        ) : record.transaction?.sent_time ? (
          dayjs(record.transaction.sent_time).format("DD/MM/YYYY")
        ) : (
          ""
        ),
    },
    {
      title: "Deskripsi",
      key: "description",
      width: 100,
      ellipsis: true,
      render: (record: TransactionItem) => (
        <div className="break-words max-w-[100px]">
          {record.transaction?.sender_notes || ""}
        </div>
      ),
    },
    {
      title: "Notes",
      key: "notes",
      width: 400,
      ellipsis: true,
      render: (record: TransactionItem) => (
        <div className="break-words max-w-[400px]">{record.notes || ""}</div>
      ),
    },
    {
      title: "Nomor",
      key: "number",
      render: (record: TransactionItem) =>
        record.transaction?.number ? (
          <a
            href={`/journal_accounts/${record.transaction.id}`}
            target="_blank"
            rel="noopener noreferrer"
            className="text-blue-600 hover:underline"
          >
            {record.transaction.number}
          </a>
        ) : (
          ""
        ),
    },
    {
      title: "Debit",
      dataIndex: "debit",
      key: "debit",
      align: "right" as const,
      render: (value: string | number) =>
        value && parseFloat(value as string) > 0
          ? new Intl.NumberFormat("id-ID").format(Number(value))
          : "0",
    },
    {
      title: "Kredit",
      dataIndex: "credit",
      key: "credit",
      align: "right" as const,
      render: (value: string | number) =>
        value && parseFloat(value as string) > 0
          ? new Intl.NumberFormat("id-ID").format(Number(value))
          : "0",
    },
    {
      title: "Saldo Berjalan",
      key: "running_balance",
      align: "right" as const,
      render: (record: TransactionItem) =>
        typeof record.running_balance === "number"
          ? new Intl.NumberFormat("id-ID").format(record.running_balance)
          : "-",
    },
  ];

  const tableData: TransactionItem[] = [
    {
      key: "saldo-awal",
      id: "Saldo Awal",
      debit: initialDebit,
      credit: initialCredit,
      running_balance: initialBalance,
    },
    ...data,
  ];

  const formatCurrency = (val: number) =>
    typeof val === "number" ? new Intl.NumberFormat("id-ID").format(val) : "-";

  return (
    <Modal open={visible} onCancel={onClose} footer={null} width={"90%"}>
      <div className="m-2 text-2xl font-bold">
        <h2>
          Transaksi {accountData?.code} : {accountData?.name}
        </h2>
      </div>
      <div className="flex justify-end mb-4 gap-2 flex-wrap">
        <RangePicker
          style={{ width: "25%" }}
          value={dateRange}
          size="large"
          onChange={(value) => {
            setDateRange(value as [Dayjs, Dayjs]);
            const newPag = { ...pagination, current: 1 };
            setPagination(newPag);
            fetchData({
              pagination: newPag,
              startDate: value ? dayjs(value[0]).format("YYYY-MM-DD") : null,
              endDate: value ? dayjs(value[1]).format("YYYY-MM-DD") : null,
            });
          }}
          allowClear
          presets={[
            {
              label: "Hari Ini",
              value: [dayjs(), dayjs()],
            },
            {
              label: "Kemarin",
              value: [dayjs().subtract(1, "day"), dayjs().subtract(1, "day")],
            },
            {
              label: "7 Hari Terakhir",
              value: [dayjs().subtract(6, "day"), dayjs()],
            },
            {
              label: "30 Hari Terakhir",
              value: [dayjs().subtract(29, "day"), dayjs()],
            },
            {
              label: "Bulan Ini",
              value: [dayjs().startOf("month"), dayjs().endOf("month")],
            },
            {
              label: "Bulan Lalu",
              value: [
                dayjs().subtract(1, "month").startOf("month"),
                dayjs().subtract(1, "month").endOf("month"),
              ],
            },
            {
              label: "Tahun Ini",
              value: [dayjs().startOf("year"), dayjs().endOf("year")],
            },
            {
              label: "Tahun Lalu",
              value: [
                dayjs().subtract(1, "year").startOf("year"),
                dayjs().subtract(1, "year").endOf("year"),
              ],
            },
          ]}
        />
      </div>

      <Table
        columns={columns}
        dataSource={tableData}
        rowKey={(row: TransactionItem) => row.id?.toString() ?? row.key!}
        loading={loading}
        pagination={false}
        scroll={{ x: "max-content" }}
        summary={() => (
          <Table.Summary fixed>
            <Table.Summary.Row>
              <Table.Summary.Cell index={0} colSpan={4}>
                <h5 className="text-lg font-bold">Saldo Akhir</h5>
              </Table.Summary.Cell>
              <Table.Summary.Cell index={4} className="text-lg" align="right">
                {formatCurrency(finalDebit)}
              </Table.Summary.Cell>
              <Table.Summary.Cell index={5} className="text-lg" align="right">
                {formatCurrency(finalCredit)}
              </Table.Summary.Cell>
              <Table.Summary.Cell index={6} className="text-lg font-bold" align="right">
                {formatCurrency(finalBalance)}
              </Table.Summary.Cell>
            </Table.Summary.Row>
          </Table.Summary>
        )}
      />

      <div className="flex justify-end mt-4 items-center">
        <span>Total transaksi: {pagination.total}</span>
        <Pagination
          current={pagination.current}
          pageSize={pagination.pageSize}
          total={pagination.total}
          showSizeChanger
          onChange={(page: number, pageSize: number) => {
            const newPag = { current: page, pageSize, total: pagination.total };
            setPagination(newPag);
            fetchData({ pagination: newPag });
          }}
        />
      </div>
    </Modal>
  );
};

export { AccountTransactionModal };
