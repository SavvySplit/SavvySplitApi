import React from 'react';
import { Document, Page } from 'react-pdf';
import Box from '@mui/material/Box';
import Typography from '@mui/material/Typography';
import Paper from '@mui/material/Paper';

interface OCRViewerProps {
  fileUrl: string;
  ocrText: string;
  aiResult: string;
}

const OCRViewer: React.FC<OCRViewerProps> = ({ fileUrl, ocrText, aiResult }) => {
  return (
    <Box sx={{ display: 'flex', justifyContent: 'space-between', gap: 2 }}>
      {/* Left Column: Document (PDF/Image) */}
      <Box sx={{ flex: '0 0 33%', maxWidth: '33%', overflow: 'auto', height: '80vh' }}>
        {/* For PDFs */}
        {fileUrl.endsWith('.pdf') ? (
          <Document file={fileUrl}>
            <Page pageNumber={1} width={window.innerWidth * 0.45} />
          </Document>
        ) : (
          // For Images, we can show an img tag
          <img src={fileUrl} alt="Attachment" style={{ maxWidth: '100%', height: 'auto' }} />
        )}
      </Box>

      {/* Right Column: OCR Text */}
      <Box sx={{ flex: '0 0 33%', maxWidth: '33%', overflow: 'auto', padding: 2 }}>
        <Paper sx={{ padding: 2, height: '100%', backgroundColor: '#f9fafb' }}>
          <Typography variant="h6" sx={{ fontWeight: 600, marginBottom: 1 }}>
            OCR Output
          </Typography>
          <Typography variant="body2" sx={{ whiteSpace: 'pre-wrap', color: '#374151', fontFamily: 'monospace' }}>
            {ocrText || 'No OCR result available.'}
          </Typography>
        </Paper>
      </Box>

      <Box sx={{ flex: '0 0 33%', maxWidth: '33%', overflow: 'auto', padding: 2 }}>
          <Paper sx={{ padding: 2, height: '100%', backgroundColor: '#f9fafb' }}>
            <Typography variant="h6" sx={{ fontWeight: 600, marginBottom: 1 }}>
              AI Output
            </Typography>
            <Typography variant="body2" sx={{ whiteSpace: 'pre-wrap', color: '#374151', fontFamily: 'monospace' }}>
              {aiResult || 'No AI result available.'}
            </Typography>
          </Paper>
        </Box>
    </Box>
  );
};

export default OCRViewer;
