"use client";

import { useState, useEffect } from 'react';
import { Select, DatePicker, Table, Flex, Button } from 'antd';
import {
  Card
} from "../../../../../components/ui/card";
import type { Dayjs } from 'dayjs';
import dayjs from 'dayjs';

// import { api } from "../../../../../api/index";
import { getApi } from "../../../../../api/index";

import { SupplyTransactionModal } from '../../../../../components/primary/supplies/SupplyTransactionModal';



const { RangePicker } = DatePicker;


interface ApiSummary {
  data: any[];
  summary_types: string[];
  spaces: any[]; 
}



// Presets untuk RangePicker (Stock Flow)
const stockFlowPresets: { label: string; value: [Dayjs, Dayjs] }[] = [
  { label: 'Hari Ini', value: [dayjs(), dayjs()] },
  { label: 'Kemarin', value: [dayjs().subtract(1, 'day'), dayjs().subtract(1, 'day')] },
  { label: '7 Hari Terakhir', value: [dayjs().subtract(6, 'day'), dayjs()] },
  { label: '30 Hari Terakhir', value: [dayjs().subtract(29, 'day'), dayjs()] },
  { label: 'Bulan Ini', value: [dayjs().startOf('month'), dayjs().endOf('month')] },
  { label: 'Bulan Lalu', value: [dayjs().subtract(1, 'month').startOf('month'), dayjs().subtract(1, 'month').endOf('month')] },
  { label: 'Tahun Ini', value: [dayjs().startOf('year'), dayjs().endOf('year')] },
  { label: 'Tahun Lalu', value: [dayjs().subtract(1, 'year').startOf('year'), dayjs().subtract(1, 'year').endOf('year')] },
  { label: '12 Bulan Terakhir', value: [dayjs().subtract(12, 'month').startOf('month'), dayjs().subtract(1, 'month').endOf('month')] },
  { label: '24 Bulan Terakhir', value: [dayjs().subtract(24, 'month').startOf('month'), dayjs().subtract(1, 'month').endOf('month')] },
];

const defaultRange = stockFlowPresets[6].value;


// Presets untuk DatePicker tunggal (Balance Stock)
const balanceStockPresets: { label: string; value: Dayjs }[] = [
  { label: 'Hari Ini', value: dayjs() },
  { label: 'Kemarin', value: dayjs().subtract(1, 'day') },
];


function format_number(num: number){
  return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
}


export default function SuppliesSummariesPage() {
  const [reportType, setReportType] = useState<string>('');
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState<any[]>([]);

  const [dateRange, setDateRange] = useState<[Dayjs, Dayjs] | null>(defaultRange);
  const [singleDate, setSingleDate] = useState<Dayjs>(dayjs());

  const [spaces, setSpaces] = useState<any[]>([]);

  const [reportShow, setReportShow] = useState<boolean>(false);


  const [selectedType, setSelectedType] = useState<string>('');
  const [modalTransactionVisible, setModalTransactionVisible] = useState<boolean>(false);
  const [modalStartDate, setModalStartDate] = useState<string>('');
  const [modalEndDate, setModalEndDate] = useState<string>('');

  const api = getApi();

  

  const handleReportTypeChange = (value: string) => {
    setReportShow(false);
    setReportType(value);
  };


  const onSaldoClick = (type: string, date: string) => {
    setSelectedType(type);

    setModalStartDate(date);
    setModalEndDate(date);
    
    setModalTransactionVisible(true);
  }


  const balanceStockColumns = [
    {
      title: 'id',
      dataIndex: 'id',
      key: 'id',
    },

    // space name
    {
      title: 'Nama Space',
      key: 'space_name',
      render: (record: any) => {
        return record.name ?? '';
      },
    },

    // inventory value
    {
      title: 'Nilai Inventaris',
      key: 'inventory_value',
      style: { textAlign: 'right' },
      render: (record: any) => {
        return new Intl.NumberFormat('id-ID', {
          maximumFractionDigits: 2,
        }).format(record.inventory_value ?? 0);
      }
    },
  ]


  const stockflowColumns = [
    // date
    {
      title: 'date', key: 'date', render (record: any) { return record.date.split(' ')[0] }
    },

    // purchase
    {
      title: 'Purchase', key: 'PO', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('PO', record.date.split(' ')[0]);
          console.log('PO');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.PO || 0 }
      </a>
    )},

    // return
    {
      title: 'Return', key: 'RTR', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('RTR', record.date.split(' ')[0]);
          console.log('RTR');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.RTR || 0 }
      </a>
    )},

    // Opname Found
    {
      title: 'Opname Found', key: 'FND', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('FND', record.date.split(' ')[0]);
          console.log('FND');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.FND || 0 }
      </a>
    )},

    // Opname Loss
    {
      title: 'Opname Loss', key: 'LOSS', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('LOSS', record.date.split(' ')[0]);
          console.log('LOSS');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.LOSS || 0 }
      </a>
    )},

    // Move
    {
      title: 'Move', key: 'MV', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('MV', record.date.split(' ')[0]);
          console.log('MV');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.MV || 0 }
      </a>
    )},

    // Sales
    {
      title: 'Sales', key: 'SO', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('SO', record.date.split(' ')[0]);
          console.log('SO');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.SO || 0 }
      </a>
    )},

    // Damage
    {
      title: 'Damage', key: 'DMG', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('DMG', record.date.split(' ')[0]);
          console.log('DMG');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.DMG || 0 }
      </a>
    )},

    // Undefined
    {
      title: 'Undefined', key: 'UNDF', style: { textAlign: 'right' }, render: (record: any) => (
      <a
        onClick={(e) => {
          e.stopPropagation();
          onSaldoClick('UNDF', record.date.split(' ')[0]);
          console.log('UNDF');
        }}
        style={{ cursor: "pointer" }}
      >
        {record.UNDF || 0 }
      </a>
    )},

    // Balance
    {
      title: 'Balance', key: 'balance', style: { textAlign: 'right' }, render (record: any) { return format_number(record.balance ?? 0) }
    },
  ]


  const handleFetchData = async () => {
    try {
      setLoading(true);

      const start = dateRange?.[0] ?? dayjs();
      const end = (reportType === 'balance_stock' ? singleDate : dateRange?.[1]) ?? dayjs();

      const res = await api.get<ApiSummary>("supplies/summary", {
        searchParams: {
          summary_type: reportType,
          start_date: start.format("YYYY-MM-DD"),
          end_date: end.format("YYYY-MM-DD"),
        },
      });
      const json = await res.json();

      console.log("json", json);

      setData(json.data);
      setSpaces(json.spaces);

      setReportShow(true);
    } catch (error) {
      console.error("Fetch error:", error);
    } finally {
      setLoading(false);
    }
  };



  useEffect(() => {
    if(!reportType) return;

    handleFetchData();
  }, [reportType, dateRange, singleDate]);



  return (
    <Card 
      title="Rangkuman Stok" 
      style={{ width: '100%' }}
    >
      
      <Flex
        gap="small"
        wrap
        justify="flex-end"
        style={{ margin: "8px 16px" }}
      >

          {reportType === 'stockflow' && (
            <RangePicker 
              presets={stockFlowPresets}
              value={dateRange}
              onChange={(dates) => setDateRange(dates as [Dayjs, Dayjs])}
            />
          )}

          {reportType === 'balance_stock' && (
            <DatePicker 
              presets={balanceStockPresets}
              value={singleDate}
              onChange={(date) => setSingleDate(date)}
            />
          )}


          <Select
            placeholder="Pilih tipe laporan"
            style={{ width: 200 }}
            onChange={handleReportTypeChange}
            value={reportType}
            options={[
              { value: '', label: 'Pilih Rangkuman' },
              { value: 'stockflow', label: 'Stock Flow' },
              { value: 'balance_stock', label: 'Balance Stock' },
            ]}
          >
          </Select>


          <Button type="primary" htmlType="submit" onClick={handleFetchData}>
            Tampilkan
          </Button>
      </Flex>

      <br />

      <h2>Rangkuman</h2>

      {reportType === 'balance_stock' &&
      reportShow && 
      (
        <Table 
          dataSource={data}
          loading={loading}
          columns={balanceStockColumns}
          pagination={false}
          rowKey="id"
          size="small"
          bordered
          summary={(data: any) => {
            let totalInventoryValue = 0;
            data.forEach((space: any) => {
              totalInventoryValue += space.inventory_value;
            });
            return (
              <Table.Summary fixed>
                <Table.Summary.Row>
                  <Table.Summary.Cell index={0} className="text-lg font-bold">Total</Table.Summary.Cell>
                  <Table.Summary.Cell index={1} className="text-lg">
                  </Table.Summary.Cell>
                  <Table.Summary.Cell index={2} className="text-lg font-bold">
                    {new Intl.NumberFormat("id-ID").format(totalInventoryValue)}
                  </Table.Summary.Cell>
                </Table.Summary.Row>
              </Table.Summary>
            );
          }}
        />
      )}



      {/* stockflow  */}
      {reportType === 'stockflow' &&
      reportShow && 
      (
        <Table 
          dataSource={data}
          loading={loading}
          columns={stockflowColumns}
          rowKey="date"
          bordered
          // summary={(data) => {
          //   let totalInventoryValue = 0;
          //   data.forEach((space: any) => {
          //     totalInventoryValue += space.inventory_value;
          //   });
          //   return (
          //     <Table.Summary fixed>
          //       <Table.Summary.Row>
          //         <Table.Summary.Cell index={0} className="text-lg font-bold">Total</Table.Summary.Cell>
          //         <Table.Summary.Cell index={1} className="text-lg">
          //         </Table.Summary.Cell>
          //         <Table.Summary.Cell index={2} className="text-lg font-bold">
          //           {new Intl.NumberFormat("id-ID").format(totalInventoryValue)}
          //         </Table.Summary.Cell>
          //       </Table.Summary.Row>
          //     </Table.Summary>
          //   );
          // }}
        />
      )}


      <SupplyTransactionModal
        visible={modalTransactionVisible}
        onClose={() => setModalTransactionVisible(false)}
        startDate={modalStartDate}
        endDate={modalEndDate}
        modelType={selectedType}
        accountId={''}
        accountData={null}
        spaces={spaces}
      />
    </Card>
  );
}