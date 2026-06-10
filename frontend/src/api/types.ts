export interface Sample {
  id: number;
  uuid: string;
  barcode: string;
  status: string;
  createdAt: string;
  customer?: { id: number; name: string };
  sampleType?: { id: number; name: string };
}

export interface TestMethod {
  id: number;
  code: string;
  name: string;
}

export interface Customer {
  id: number;
  name: string;
  inn?: string;
  contactPerson?: string;
  phone?: string;
  email?: string;
}

export interface SampleTest {
  id: number;
  status: string;
  resultValue?: string;
  testMethod: TestMethod;
  approvedBy?: { name: string };
}
