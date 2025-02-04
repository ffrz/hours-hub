import { usePage } from "@inertiajs/vue3";
import dayjs from "dayjs";

export function format_duration(duration) {
  const durationInt = parseInt(duration);
  const hours = Math.floor(durationInt / 3600);
  const minutes = Math.floor((durationInt % 3600) / 60);
  const seconds = durationInt % 60;
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}
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

export function create_options_v2(items, valueProp, labelProp) {
  return items.map((item) => {
    return { 'value': item[valueProp], 'label': item[labelProp] };
  });
}


export function create_options_from_users(items) {
  return items.map((user) => {
    return { 'value': user.id, 'label': `${user.username} - ${user.name}` };
  });
}

export function create_options_from_clients(items) {
  return items.map((item) => {
    return { 'value': item.id, 'label': `${item.name}` };
  });
}

export async function scrollToFirstErrorField(ref) {
  const element = ref.getNativeElement();
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    element.focus();
  }
}

export function format_datetime(dt, format = 'DD-MM-YYYY HH:mm:ss') {
  return dayjs(dt).format(format)
}
