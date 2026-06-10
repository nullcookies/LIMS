import { useQuery } from '@tanstack/react-query';
import { Box, Card, CardContent, Typography, Grid, Chip, Table, TableBody, TableCell, TableHead, TableRow, Alert, CircularProgress } from '@mui/material';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from 'recharts';
import { endpoints } from '../api/endpoints';

const COLORS = ['#1976d2', '#388e3c', '#f57c00', '#d32f2f', '#7b1fa2', '#00796b', '#c2185b', '#455a64'];
const STATUS_COLORS: Record<string, string> = {
  registered: '#1976d2',
  in_progress: '#f57c00',
  completed: '#388e3c',
  approved: '#2e7d32',
  rejected: '#d32f2f',
  pending: '#757575',
};

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <Card sx={{ mb: 3 }}>
      <CardContent>
        <Typography variant="h6" gutterBottom>{title}</Typography>
        {children}
      </CardContent>
    </Card>
  );
}

export default function ReportsPage() {
  const sampleActivity = useQuery({ queryKey: ['report-sample-activity'], queryFn: () => endpoints.reports.sampleActivity().then(r => r.data) });
  const testResults = useQuery({ queryKey: ['report-test-results'], queryFn: () => endpoints.reports.testResults().then(r => r.data) });
  const customerSummary = useQuery({ queryKey: ['report-customer-summary'], queryFn: () => endpoints.reports.customerSummary().then(r => r.data) });
  const instrumentStatus = useQuery({ queryKey: ['report-instrument-status'], queryFn: () => endpoints.reports.instrumentStatus().then(r => r.data) });

  const loading = sampleActivity.isLoading || testResults.isLoading || customerSummary.isLoading || instrumentStatus.isLoading;
  const error = sampleActivity.error || testResults.error || customerSummary.error || instrumentStatus.error;

  if (loading) return <Box sx={{ display: 'flex', justifyContent: 'center', mt: 4 }}><CircularProgress /></Box>;
  if (error) return <Alert severity="error">Failed to load reports</Alert>;

  const sa = sampleActivity.data;
  const tr = testResults.data;
  const cs = customerSummary.data;
  const inst = instrumentStatus.data;

  return (
    <Box>
      <Typography variant="h5" gutterBottom>Reports & Analytics</Typography>
      <Grid container spacing={3}>
        <Grid item xs={12} md={6}>
          <Section title="Sample Registrations Over Time">
            <ResponsiveContainer width="100%" height={250}>
              <LineChart data={sa?.daily || []}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="date" tick={{ fontSize: 11 }} />
                <YAxis allowDecimals={false} />
                <Tooltip />
                <Line type="monotone" dataKey="count" stroke="#1976d2" strokeWidth={2} dot={{ r: 3 }} />
              </LineChart>
            </ResponsiveContainer>
          </Section>
        </Grid>

        <Grid item xs={12} md={6}>
          <Section title="Sample Status Breakdown">
            <ResponsiveContainer width="100%" height={250}>
              <PieChart>
                <Pie data={sa?.statusBreakdown || []} dataKey="count" nameKey="status" cx="50%" cy="50%" outerRadius={90} label={({ status, count }) => `${status}: ${count}`}>
                  {(sa?.statusBreakdown || []).map((_: unknown, i: number) => (
                    <Cell key={i} fill={STATUS_COLORS[(_ as { status: string }).status] || COLORS[i % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </Section>
        </Grid>

        <Grid item xs={12} md={6}>
          <Section title="Tests by Method">
            <ResponsiveContainer width="100%" height={250}>
              <BarChart data={tr?.byMethod || []}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="method" tick={{ fontSize: 11 }} />
                <YAxis allowDecimals={false} />
                <Tooltip />
                <Bar dataKey="count" fill="#388e3c" />
              </BarChart>
            </ResponsiveContainer>
          </Section>
        </Grid>

        <Grid item xs={12} md={6}>
          <Section title="Test Status Breakdown">
            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1, mt: 1 }}>
              {tr?.byStatus?.map((s: { status: string; count: number }) => (
                <Chip key={s.status} label={`${s.status}: ${s.count}`} sx={{ bgcolor: STATUS_COLORS[s.status], color: '#fff' }} />
              ))}
            </Box>
            <Typography variant="body2" sx={{ mt: 2, color: 'text.secondary' }}>Total tests: {tr?.total || 0}</Typography>
          </Section>
        </Grid>

        <Grid item xs={12} md={6}>
          <Section title="Samples per Customer">
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={cs || []} layout="vertical">
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis type="number" allowDecimals={false} />
                <YAxis type="category" dataKey="name" width={120} tick={{ fontSize: 11 }} />
                <Tooltip />
                <Bar dataKey="sampleCount" fill="#f57c00" />
              </BarChart>
            </ResponsiveContainer>
          </Section>
        </Grid>

        <Grid item xs={12} md={6}>
          <Section title="Instrument Calibration Status">
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Name</TableCell>
                  <TableCell>Model</TableCell>
                  <TableCell>Last Calibration</TableCell>
                  <TableCell>Status</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {(inst || []).map((i: { name: string; model: string; lastCalibration: string | null; overdue: boolean }) => (
                  <TableRow key={i.name}>
                    <TableCell>{i.name}</TableCell>
                    <TableCell>{i.model}</TableCell>
                    <TableCell>{i.lastCalibration || 'Never'}</TableCell>
                    <TableCell>
                      <Chip size="small" label={i.overdue ? 'Overdue' : 'OK'} color={i.overdue ? 'error' : 'success'} />
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </Section>
        </Grid>
      </Grid>
    </Box>
  );
}
