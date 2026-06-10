import { Box, Typography, Grid, Card, CardContent } from '@mui/material';
import { Assessment, Download, Print } from '@mui/icons-material';
import { Button } from '@mui/material';

const reports = [
  { label: 'Sample Activity Report', desc: 'Summary of all samples registered in the system' },
  { label: 'Test Results Report', desc: 'Completed test results with QC status' },
  { label: 'Customer Report', desc: 'Customer list with sample counts' },
  { label: 'Instrument Usage Report', desc: 'Instrument utilization over time' },
];

export default function ReportsPage() {
  return (
    <Box>
      <Typography variant="h4" gutterBottom>Reports</Typography>
      <Grid container spacing={3}>
        {reports.map((r) => (
          <Grid item xs={12} sm={6} key={r.label}>
            <Card>
              <CardContent>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
                  <Assessment color="primary" />
                  <Typography variant="h6">{r.label}</Typography>
                </Box>
                <Typography color="text.secondary" sx={{ mb: 2 }}>{r.desc}</Typography>
                <Box sx={{ display: 'flex', gap: 1 }}>
                  <Button size="small" startIcon={<Download />}>Download</Button>
                  <Button size="small" startIcon={<Print />}>Print</Button>
                </Box>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}
