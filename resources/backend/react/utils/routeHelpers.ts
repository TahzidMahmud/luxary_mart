
export function route(name: string, params: Record<string, string | number> = {}): string {
  const routeMap: Record<string, string> = {
    'admin.orders.downloadInvoice': '/admin/invoice-download/:id',
    // Add more routes here if needed
  };

  let path = routeMap[name];

  if (!path) {
    throw new Error(`Route "${name}" is not defined.`);
  }

  Object.keys(params).forEach((key) => {
    path = path.replace(`:${key}`, encodeURIComponent(String(params[key])));
  });

  return path;
}
