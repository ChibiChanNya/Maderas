const orders = [
  {
    id: '1', provider_id: 1, material_id: 1, amount: 3, order_date: new Date().toISOString().slice(0, 10),
    delivery_date: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
    total_cost: 5000, status: "pending", remaining_cost: 5000, invoice_id: null
  },

  {
    id: '2',
    provider_id: 1,
    material_id: 1,
    amount: 5,
    order_date: new Date(new Date().getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
    delivery_date: new Date().toISOString().slice(0, 10),
    total_cost: 3000,
    status: "delivered",
    remaining_cost: 2500,
    invoice_id: null
  },

  {
    id: '3',
    provider_id: 2,
    material_id: 1,
    amount: 8,
    order_date: new Date(new Date().getTime() - 50 * 60 * 60 * 1000).toISOString().slice(0, 10),
    delivery_date: new Date(new Date().getTime() - 14 * 60 * 60 * 1000).toISOString().slice(0, 10),
    total_cost: 7000,
    status: "paid",
    remaining_cost: 0,
    invoice_id: null
  },

];

export default orders;