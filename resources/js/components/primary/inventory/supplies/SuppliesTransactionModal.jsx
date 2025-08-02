import { useEffect, useState } from "react";
import { Modal, Table, DatePicker, Pagination } from "antd";
import axios from "axios";
import dayjs from "dayjs";

const { RangePicker } = DatePicker;

const SupplyTransactionModal = ({ visible, onClose, accountId, startDate, endDate, accountData }) => {
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState([]);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 10 });
  const [search, setSearch] = useState("");
  const [dateRange, setDateRange] = useState(null);

  const [initialDebit, setInitialDebit] = useState(0);
  const [initialCredit, setInitialCredit] = useState(0);
  const [initialBalance, setInitialBalance] = useState(0);
  const [finalDebit, setFinalDebit] = useState(0);
  const [finalCredit, setFinalCredit] = useState(0);
  const [finalBalance, setFinalBalance] = useState(0);


  const fetchData = async (params = {}) => {
    setLoading(true);
    try {
      const res = await axios.get("/supplies/supply_transactions", {
        params: {
          account_id: accountId,
          search: search,
          start_date: params.startDate || dateRange?.[0]?.format("YYYY-MM-DD") || startDate,
          end_date: params.endDate || dateRange?.[1]?.format("YYYY-MM-DD") || endDate,
          page: params.pagination?.current || 1,
          per_page: params.pagination?.pageSize || pagination.pageSize,
        },
      });
      console.log(params, res.data);


      let runningBalance = parseFloat(res.data.initial_balance || 0);
      let runningDebit = parseFloat(res.data.initial_debit || 0);
      let runningCredit = parseFloat(res.data.initial_credit || 0);
      const calculatedData = res.data.data.map((item) => {
        const debit = parseFloat(item.debit || 0);
        const credit = parseFloat(item.credit || 0);

        runningDebit += debit;
        runningCredit += credit;
        runningBalance += debit - credit;
        
        return {
          ...item,
          running_balance: runningBalance,
        };
      });

      setInitialBalance(parseFloat(res.data.initial_balance || 0));
      setInitialDebit(parseFloat(res.data.initial_debit || 0));
      setInitialCredit(parseFloat(res.data.initial_credit || 0));

      // setFinalDebit(runningDebit);
      // setFinalCredit(runningCredit);
      setFinalDebit(parseFloat(res.data.page_debit || 0));
      setFinalCredit(parseFloat(res.data.page_credit || 0));
      setFinalBalance(runningBalance);
      
      setData(calculatedData);
      setPagination((prev) => ({
        current: params.pagination?.current || prev.current,
        pageSize: params.pagination?.pageSize || prev.pageSize,
        total: res.data.total,
      }));
    } catch (err) {
      console.error(err);
    }
    setLoading(false);
  };


  // set initial range when modal opens
  useEffect(() => {
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
      render: (_, record) =>
        record.key == 'saldo-awal' ? (
          <span className="text-lg font-bold">Saldo Awal</span>   
        ) :
        record.transaction?.sent_time
          ? dayjs(record.transaction.sent_time).format("DD/MM/YYYY")
          : "",
    },

    // Number
    {
      title: "Nomor",
      key: "number",
      render: (_, record) => (
        record.transaction?.number ? (
          <a
            href={`/journal_supplies/${record.transaction.id}`}
            target="_blank"
            rel="noopener noreferrer"
            className="text-blue-600 hover:underline"
          >
            {record.transaction.number}
          </a>
        ) : ""
      ),
    },

    // Description
    {
      title: "Deskripsi",
      key: "description",
      width: 100,
      ellipsis: true,
      render: (_, record) => (
        <div className="break-words max-w-[100px]">
          {record.transaction?.sender_notes || ""}<br />
          {record.transaction?.handler_notes || ""}
        </div>
      ),
    },

    // Notes
    {
      title: "Notes",
      key: "notes",
      width: 100,
      ellipsis: true,
      render: (_, record) => (
        <div className="break-words max-w-[400px]">
          {record.notes || ""}
        </div>
      ),
    },

    // model_type
    {
      title: "Jenis",
      key: "model_type",
      render: (_, record) => (
        record?.model_type || ""
      ), 
    },
    
    // cost_per_unit
    {
      title: "Modal",
      dataIndex: "cost_per_unit",
      key: "cost_per_unit",
      align: "right",
      render: (value) =>
        value && parseFloat(value) >= 0
          ? new Intl.NumberFormat("id-ID").format(value)
          : "0",
    },

    {
      title: "Debit",
      dataIndex: "debit",
      key: "debit",
      align: "right",
      render: (value) =>
        value && parseFloat(value) > 0
          ? new Intl.NumberFormat("id-ID").format(value)
          : "0",
    },
    {
      title: "Kredit",
      dataIndex: "credit",
      key: "credit",
      align: "right",
      render: (value) =>
        value && parseFloat(value) > 0
          ? new Intl.NumberFormat("id-ID").format(value)
          : "0",
    },
    {
      title: "Saldo Berjalan",
      key: "running_balance",
      align: "right",
      render: (_, record) =>
        typeof record.running_balance === "number"
          ? new Intl.NumberFormat("id-ID").format(record.running_balance)
          : record.running_balance,
    },
  ];

  const tableData = [
    {
      key: "saldo-awal",
      id: "Saldo Awal",
      date: null,
      description: null,
      notes: null,
      number: null,
      model_type: null,
      cost_per_unit: null,
      debit: initialDebit,
      credit: initialCredit,
      running_balance: initialBalance,
    },
    ...data,
  ];


  const formatCurrency = (val) =>
  typeof val === "number"
    ? new Intl.NumberFormat("id-ID").format(val)
    : "-";


  return (
    <Modal
      visible={visible}
      onCancel={onClose}
      footer={null}
      width={"90%"}
    > 
      <div className="m-2 text-2xl font-bold">
        <h2>Mutasi Stok {accountData?.sku} : {accountData?.name}</h2>
      </div>
      <div className="flex justify-end mb-4 gap-2 flex-wrap">
        <RangePicker
          className=""
          style={{ width: "25%" }}
          value={dateRange}
          size="large"
          onChange={(value) => {
            setDateRange(value);
            const newPag = { ...pagination, current: 1 };
            setPagination(newPag);
            fetchData({
              pagination: { ...pagination, current: 1 },
              startDate: value ? dayjs(value[0]).format("YYYY-MM-DD") : null,
              endDate: value ? dayjs(value[1]).format("YYYY-MM-DD") : null,
            });
          }}
          allowClear
          presets={[
            {
              label: 'Hari Ini',
              value: [dayjs(), dayjs()],
            },
            {
              label: 'Kemarin',
              value: [dayjs().subtract(1, 'day'), dayjs().subtract(1, 'day')],
            },
            {
              label: '7 Hari Terakhir',
              value: [dayjs().subtract(6, 'day'), dayjs()],
            },
            {
              label: '30 Hari Terakhir',
              value: [dayjs().subtract(29, 'day'), dayjs()],
            },
            {
              label: 'Bulan Ini',
              value: [dayjs().startOf('month'), dayjs().endOf('month')],
            },
            {
              label: 'Bulan Lalu',
              value: [
                dayjs().subtract(1, 'month').startOf('month'),
                dayjs().subtract(1, 'month').endOf('month'),
              ],
            },
            {
              label: 'Tahun Ini',
              value: [dayjs().startOf('year'), dayjs().endOf('year')],
            },
            {
              label: 'Tahun Lalu',
              value: [
                dayjs().subtract(1, 'year').startOf('year'),
                dayjs().subtract(1, 'year').endOf('year'),
              ],
            },
            {
              label: '1 Tahun Lalu (12 Bulan)',
              value: [
                dayjs().subtract(12, 'month').startOf('month'),
                dayjs().subtract(1, 'month').endOf('month'),
              ],
            },
            {
              label: '2 Tahun Lalu (24 Bulan)',
              value: [
                dayjs().subtract(24, 'month').startOf('month'),
                dayjs().subtract(1, 'month').endOf('month'),
              ],
            },
          ]}
        />
      </div>


      <Table
        columns={columns}
        dataSource={tableData}
        rowKey={(row) => row.id || row.key}
        loading={loading}
        pagination={false}
        scroll={{ x: "max-content" }}
        summary={() => (
          <Table.Summary fixed>
            <Table.Summary.Row>
              <Table.Summary.Cell colSpan={6}><h5 className="text-lg font-bold">Saldo Akhir</h5></Table.Summary.Cell>
              <Table.Summary.Cell className="text-lg" align="right">
                {formatCurrency(finalDebit)}
              </Table.Summary.Cell>
              <Table.Summary.Cell className="text-lg" align="right">
                {formatCurrency(finalCredit)}
              </Table.Summary.Cell>
              <Table.Summary.Cell className="text-lg font-bold" align="right">
                {new Intl.NumberFormat("id-ID").format(finalBalance)}
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
          onChange={(page, pageSize) => {
            const newPag = { current: page, pageSize };
            setPagination(newPag);
            fetchData({ pagination: newPag });
          }}
        />
      </div>

    </Modal>
  );
};

export default SupplyTransactionModal;
