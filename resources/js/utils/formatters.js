// utils/formatters.js
export const formatDateTime = (dateString) => {
  const options = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit', 
    minute: '2-digit',
    hour12: true
  }
  return new Date(dateString).toLocaleString('en-US', options)
}

export const formatCurrency = (amount, currency = 'MMK') => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency,
    minimumFractionDigits: 0
  }).format(amount).replace(currency, '') + ` ${currency}`
}

export const statusBadgeClasses = (status) => {
  const classes = {
    base: 'text-xs font-semibold px-3 py-1 rounded-full capitalize',
    won: 'bg-green-100 text-green-800',
    lost: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800',
    refunded: 'bg-gray-100 text-gray-800'
  }
  return `${classes.base} ${classes[status] || classes.pending}`
}