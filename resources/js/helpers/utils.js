import { usePage } from "@inertiajs/vue3";

/**
 * Memeriksa apakah current user role ada di roles
 * @param {string | Array} roles
 * @returns boolean
 */
export function check_role(roles) {
  const page = usePage();
  if (!Array.isArray(roles))
    roles = [roles];
  return roles.includes(page.props.auth.user.role);
}

export function create_options(data) {
  return Object.entries(data)
    .map(([key, value]) => ({ 'value': key, 'label': value }));
}

export function create_options_from_users(items) {
  return items.map((user) => {
    return { 'value': user.id, 'label': `${user.username} - ${user.name}` };
  });
}

export function create_options_from_technicians(items) {
  return items.map((technician) => {
    return { 'value': technician.id, 'label': `#${technician.id} - ${technician.name}` };
  });
}

export function create_options_from_customers(items) {
  return items.map((customer) => {
    return { 'value': customer.id, 'label': `#${customer.id}: ${customer.name}` };
  });
}

export async function scrollToFirstErrorField(ref) {
  const element = ref.getNativeElement();
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    element.focus();
  }
}
