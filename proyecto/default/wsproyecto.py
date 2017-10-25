import logging
logging.basicConfig(level=logging.DEBUG)
from spyne import ComplexModel, Unicode, Integer, Array, TTableModel, rpc
from spyne.application import Application
from spyne.decorator import srpc
from spyne.service import ServiceBase
from spyne.model.primitive import Integer
from spyne.model.primitive import Unicode
from spyne.model.complex import Iterable
from spyne.protocol.soap import Soap11
from spyne.server.wsgi import WsgiApplication
from spyne.protocol.http import HttpRpc
from spyne.server.wsgi import WsgiApplication
import psycopg2
from xml.dom import minidom
import traceback
# -*- coding: UTF-8 -*-

def conexionPostgresPsycopg(csql):
    db = psycopg2.connect(host='10.160.1.229', database='prod_registrocivil', user='postgres', password='postgres', port='61432')
    c = db.cursor()
    c.execute(csql)
    db.commit()
    c.close()
    db.close()
    return

def conexionPsycopg(csql):
    db = psycopg2.connect(host='10.160.1.229', database='prod_registrocivil', user='postgres', password='postgres', port='61432')
    c = db.cursor()
    r=c.execute(csql)
    tupla = c.fetchall()
    c.close()
    db.close()
    return tupla

class Objetos(ComplexModel):
    persona = Unicode
    apellido = Unicode
    dni = Unicode
    nroacta = Unicode
    nrolibro = Unicode
    fecha_nacimiento = Unicode
    ubicacion = Unicode
    nombre = Unicode

class RCWebService(ServiceBase):
    #@srpc(Unicode, Integer, _returns=Iterable(Unicode))
    #def diga_hola(name, times):
    #    for i in range(times):
    #        yield 'Hola, %s' % name

    #@srpc(Unicode, Unicode, Unicode, _returns=Iterable(Unicode))
    #def diga_otro_hola(cuit, cuil, xml):

    #    yield 'el cuit es %s'%cuit

    @srpc(Unicode, Unicode, Unicode, _returns=Iterable(Objetos))
    def nacimiento_propia(dni,tipo,parentesco):
	print dni, tipo, parentesco
        persona = None
        if tipo == '1' and parentesco == '1': 

            csql = """
                            select dato.nombre, dato.apellido, doc.numero, a.numero, l.numero, c.fecha_nacimiento, i.ubicacion,i.nombre 
                            from ciud_ciudadano_documento doc
                            join ciud_ciudadano_dato dato on dato.ciud_ciudadano_id = doc.ciud_ciudadano_id
                            join acta_acta_ciudadano ac on ac.ciud_ciudadano_dato_id = dato.id 
                            join ciud_ciudadano c on c.ciud_ciudadano_dato_id = dato.id
                            join acta_acta a on a.id = ac.acta_acta_id
                            join base_libro l on l.id = a.base_libro_id
                            join enlace_acta_imagen ei on ei.acta_acta_id = a.id
                            join enlace_imagen i on i.id = ei.enlace_imagen_id
                            where doc.numero = '%s'
                            and l.base_tipo_libro_id = %s
                            group by 1,2,3,4,5,6,7,8
                    """%(dni,tipo)
#            print csql
            persona = conexionPsycopg(csql)
        if tipo == '1' and parentesco == '6':
    
            csql = """
                            select ac.ciud_ciudadano_relacion_dato_id

			from ciud_ciudadano_documento doc
			join ciud_ciudadano_dato dato on dato.ciud_ciudadano_id = doc.ciud_ciudadano_id
			join acta_acta_ciudadano ac on ac.ciud_ciudadano_dato_id = dato.id 
			--join acta_acta_ciudadano ac on ac.ciud_ciudadano_relacion_dato_id = dato.id 
			join acta_acta a on a.id = ac.acta_acta_id
			join base_libro l on l.id = a.base_libro_id
			left join enlace_acta_imagen ei on ei.acta_acta_id = a.id
			left join enlace_imagen i on i.id = ei.enlace_imagen_id
			where doc.numero = '%s'
			and ac.base_tipo_rol_id = %s
			group by 1
                    """%(dni,parentesco)
 #           print csql
            persona = conexionPsycopg(csql)


            datoid = persona[0][0]
            csql = """select dato.nombre, dato.apellido, doc.numero, a.numero, l.numero, c.fecha_nacimiento, i.ubicacion,i.nombre

			from ciud_ciudadano_documento doc
			join ciud_ciudadano_dato dato on dato.ciud_ciudadano_id = doc.ciud_ciudadano_id
			join acta_acta_ciudadano ac on ac.ciud_ciudadano_dato_id = dato.id 
                        join ciud_ciudadano c on c.ciud_ciudadano_dato_id = dato.id
			--join acta_acta_ciudadano ac on ac.ciud_ciudadano_relacion_dato_id = dato.id 
			join acta_acta a on a.id = ac.acta_acta_id
			left join base_libro l on l.id = a.base_libro_id
			left join enlace_acta_imagen ei on ei.acta_acta_id = a.id
			left join enlace_imagen i on i.id = ei.enlace_imagen_id
			where ac.ciud_ciudadano_dato_id=%d
			and l.base_tipo_libro_id = 1
			group by 1,2,3,4,5,6,7,8
                    """%(datoid)
#            print csql
            persona = conexionPsycopg(csql)

        valido = True
        resultado = 'correcto'
        print persona
        if persona == None or len(persona)==0:
            resultado = 'error_persona'
            valido = False 
            return
        

#        print resultado
        array=[]
        
        for f in persona:
            #print f
            
            persona =  unicode(f[0].replace('"',''), 'utf-8')
            apellido = unicode(f[1].replace('"',''), 'utf-8')
            p = Objetos()
            p.persona = persona	
            p.apellido = apellido
            p.dni = str(f[2])
            p.nroacta = str(f[3])
            p.nrolibro = str(f[4])
            fecha = str(f[5].day).zfill(2)+"/"+str(f[5].month).zfill(2)+"/"+str(f[5].year) if f[5] is not None and f[5] !=''  else ''
            p.fecha_nacimiento = str(fecha)
            p.ubicacion = str(f[6])
            p.nombre = str(f[7])
            print p
            array.append(p)
            yield p

application = Application([RCWebService],
    tns='coyote1.rc.mendoza.gov.ar',
    in_protocol=Soap11(validator='lxml'),
    out_protocol=Soap11()
)
if __name__ == '__main__':

    from wsgiref.simple_server import make_server
    wsgi_app = WsgiApplication(application)
    server = make_server('localhost', 8000, wsgi_app)
    server.serve_forever()
