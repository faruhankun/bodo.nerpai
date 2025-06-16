import React, { useEffect, useState } from "react";
import { Modal, Table, Input, DatePicker, Button } from "antd";
import axios from "axios";
import dayjs from "dayjs";

const { RangePicker } = DatePicker;

const AccountTransactionModal = ({ visible, onClose, accountId, startDate, endDate }) => {
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState([]);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 10 });
  const [search, setSearch] = useState("");
  const [dateRange, setDateRange] = useState(null);

  const fetchData = async (params = {}) => {
    setLoading(true);
    try {
      const res = await axios.get("/accountsp/account_transactions", {
        params: {
          account_id: accountId,
          search: search,
          start_date: params.startDate || startDate,
          end_date: params.endDate || endDate,
          page: params.pagination?.current || 1,
        },
      });
      console.log(res.data)


      let runningBalance = 0;
      const calculatedData = res.data.data.map((item) => {
        const debit = parseFloat(item.debit || 0);
        const credit = parseFloat(item.credit || 0);
        runningBalance += debit - credit;

        return {
          ...item,
          running_balance: runningBalance,
        };
      });

      setData(calculatedData);
      setPagination({
        ...params.pagination,
        total: res.data.total,
      });
    } catch (err) {
      console.error(err);
    }
    setLoading(false);
  };

  useEffect(() => {
    if (visible) {
      fetchData({ pagination });
    }
  }, [visible]);

  const handleTableChange = (pag) => {
    fetchData({ pagination: pag });
  };

  const handleSearch = () => {
    fetchData({
      pagination: { ...pagination, current: 1 },
      startDate: dateRange ? dayjs(dateRange[0]).format("YYYY-MM-DD") : null,
      endDate: dateRange ? dayjs(dateRange[1]).format("YYYY-MM-DD") : null,
    });
  };

  const columns = [
    {
      title: "Tanggal",
      key: "date",
      render: (_, record) =>
        record.transaction?.sent_time
          ? dayjs(record.transaction.sent_time).format("DD/MM/YYYY")
          : "-",
    },
      {
      title: "Sumber",
      key: "source",
      render: (_, record) => record.sender_notes || record.sender_type || "-",
    },
    {
      title: "Deskripsi",
      key: "description",
      render: (_, record) => record.notes || "-",
    },
    {
      title: "Referensi",
      key: "reference",
      render: (_, record) => record.handler_number || record.model_type || "-",
    },
    {
      title: "Nomor",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Debit",
      dataIndex: "debit",
      key: "debit",
      align: "right",
      render: (value) =>
        value && parseFloat(value) > 0
          ? new Intl.NumberFormat("id-ID").format(value)
          : "-",
    },
    {
      title: "Kredit",
      dataIndex: "credit",
      key: "credit",
      align: "right",
      render: (value) =>
        value && parseFloat(value) > 0
          ? new Intl.NumberFormat("id-ID").format(value)
          : "-",
    },
    {
      title: "Saldo Berjalan",
      key: "running_balance",
      align: "right",
      render: (_, record) =>
        typeof record.running_balance === "number"
          ? new Intl.NumberFormat("id-ID").format(record.running_balance)
          : "-",
    },
  ];

  return (
    <Modal
      title="Transaksi Akun"
      visible={visible}
      onCancel={onClose}
      footer={null}
      width={1000}
    >
      <div className="flex justify-between mb-4 gap-2 flex-wrap">
        <Input
          placeholder="Cari..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-1/3"
        />
        <RangePicker
          className="w-1/3"
          onChange={(value) => setDateRange(value)}
          allowClear
        />
        <Button type="primary" onClick={handleSearch}>
          Cari
        </Button>
      </div>
      <Table
        columns={columns}
        dataSource={data}
        rowKey="id"
        loading={loading}
        pagination={pagination}
        onChange={handleTableChange}
        scroll={{ x: "max-content" }}
      />
    </Modal>
  );
};

export default AccountTransactionModal;
