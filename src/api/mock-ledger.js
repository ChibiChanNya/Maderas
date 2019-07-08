const items = [
  {id: '1', type: 'ingreso', recipient: 'Empresa ABCD', concept: 'Venta de pedido #12345', amount: 52000, date: new Date().toISOString().slice(0, 10), user: 1},
  {id: '2', type: 'salida', recipient: 'Hola S.A. de C.V.', concept: 'Lorem Ipsum dolor sit amet', amount: 1200, date: new Date().toISOString().slice(0, 10), user: 2},
  {id: '3', type: 'pago', recipient: 'Phoenix Development', concept: 'Desarrollo de App', amount: 9800, date: new Date().toISOString().slice(0, 10), user: 1},
];

export default items;