const orders = [
  {
    id: '1',
    provider_id: 1,
    request_date: new Date().toISOString().slice(0, 10),
    description: "Empresa 1 TEST",
    delivery_date: null,
    total_cost: 5000,
    status: "pendiente",
    money_debt: null,
    invoice: null,
    payment_date: null,
    order_details: [],
  },

  {
    id: '2',
    provider_id: 1,
    description: "de 6 a 9 pulgadas",
    request_date: new Date(new Date().getTime() - 24 * 60 * 60 * 1000).toISOString().slice(0, 10),
    delivery_date: new Date().toISOString().slice(0, 10),
    total_cost: 3000,
    status: "entregado",
    money_debt: 2500,
    invoice: "9213994",
    payment_date: new Date(new Date().getTime() + 124 * 60 * 60 * 1000).toISOString().slice(0, 10),
    order_details: [
      {material_id: 1, units: 5},
      {material_id: 3, units: 30},
    ]
  },

  {
    id: '3',
    provider_id: 1,
    description: "50 unidades",
    request_date: new Date(new Date().getTime() - 50 * 60 * 60 * 1000).toISOString().slice(0, 10),
    delivery_date: new Date(new Date().getTime() - 14 * 60 * 60 * 1000).toISOString().slice(0, 10),
    total_cost: 7000,
    status: "entregado",
    money_debt: 5000,
    invoice: "5435345",
    payment_date: null,
    order_details: [
      {material_id: 2, units: 50},
    ]
  },

  {
    id: '4',
    provider_id: 1,
    description: "20 unidades",
    request_date: new Date(new Date().getTime() - 30 * 60 * 60 * 1000).toISOString().slice(0, 10),
    delivery_date: new Date(new Date().getTime() - 4 * 60 * 60 * 1000).toISOString().slice(0, 10),
    total_cost: 7000,
    status: "pagado",
    money_debt: 0,
    invoice: "5435345",
    payment_date: new Date(new Date().getTime() -  60 * 60 * 1000).toISOString().slice(0, 10),
    order_details: [
      {material_id: 2, units: 20},
    ]
  },

];

export default orders;