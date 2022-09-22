import { TestBed } from '@angular/core/testing';

import { SalasOldService } from './salas-old.service';

describe('SalasOldService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: SalasOldService = TestBed.get(SalasOldService);
    expect(service).toBeTruthy();
  });
});
