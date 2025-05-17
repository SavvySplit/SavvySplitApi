import { useEffect, useRef } from 'react'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

export default function EmailChart({ labels, data }) {
  const ref = useRef()

  useEffect(() => {
    const chart = new Chart(ref.current, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Emails',
            data,
            borderColor: 'blue',
            backgroundColor: 'rgba(0, 0, 255, 0.1)',
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true },
        },
      },
    })
    return () => chart.destroy()
  }, [labels, data])

  return <canvas ref={ref} height="100"></canvas>
}
