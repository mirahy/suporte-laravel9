import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioInsercaoUsuariosMoodleComponent } from './formulario-insercao-usuarios-moodle.component';

describe('FormularioInsercaoUsuariosMoodleComponent', () => {
	let component: FormularioInsercaoUsuariosMoodleComponent;
	let fixture: ComponentFixture<FormularioInsercaoUsuariosMoodleComponent>;

	beforeEach(async(() => {
		TestBed.configureTestingModule({
			declarations: [FormularioInsercaoUsuariosMoodleComponent]
		})
			.compileComponents();
	}));

	beforeEach(() => {
		fixture = TestBed.createComponent(FormularioInsercaoUsuariosMoodleComponent);
		component = fixture.componentInstance;
		fixture.detectChanges();
	});

	it('should create', () => {
		expect(component).toBeTruthy();
	});
});
