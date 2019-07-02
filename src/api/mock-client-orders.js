const orders = [
  {
    id: '1',
    client_id: '1',
    contract: "1244345324",
    request_date: new Date().toISOString().slice(0, 10),
    description: "Primer Pedido TEST",
    finish_date: null,
    total_cost: 5000,
    status: "pendiente",
    money_debt: null,
    invoice: null,
    payment_date: null,
    order_details: [
      {product_id: '1', units: 10},
      {product_id: '2', units: 30},
    ]
  },


];

export default orders;